<?php

namespace App\Controllers;

use App\Models\EkaActivityLog;
use App\Models\EkaDagitimKaydi;
use App\Models\EkaUygulama;
use App\Services\EkaDokployServisi;
use Core\EkaAuth;
use Core\EkaController;
use Core\EkaTenant;
use Throwable;

class EkaUygulamaIslemController extends EkaController
{
    public function start()
    {
        return $this->durumIslemi('start');
    }

    public function stop()
    {
        return $this->durumIslemi('stop');
    }

    public function sync()
    {
        $tenantId = (int) EkaTenant::id();
        $id = (int) ($_POST['id'] ?? 0);
        $model = new EkaUygulama();
        $uygulama = $model->tenantUygulama($id, $tenantId);

        if (!$uygulama || empty($uygulama['dokploy_application_id'])) {
            $_SESSION['error'] = 'Uygulama bulunamadı.';
            return $this->redirect('/uygulamalar');
        }

        try {
            $uzak = (new EkaDokployServisi())->hamIstek('GET', '/application.one', [
                'applicationId' => (string) $uygulama['dokploy_application_id'],
            ]);
            $uzakDurum = (string) ($uzak['applicationStatus'] ?? $uzak['application']['applicationStatus'] ?? 'idle');
            $durum = match ($uzakDurum) {
                'running', 'done' => 'running',
                'error' => 'error',
                default => 'stopped',
            };

            $model->update($id, [
                'durum' => $durum,
                'son_hata' => $durum === 'error' ? ($uygulama['son_hata'] ?? 'Uzak uygulama hata durumunda.') : null,
                'last_sync_at' => date('Y-m-d H:i:s'),
            ]);
            $_SESSION['success'] = 'Uygulama durumu senkronize edildi.';
        } catch (Throwable $e) {
            $_SESSION['error'] = 'Durum senkronize edilemedi: ' . $e->getMessage();
        }

        return $this->redirect('/uygulamalar');
    }

    public function delete()
    {
        $tenantId = (int) EkaTenant::id();
        $id = (int) ($_POST['id'] ?? 0);
        $model = new EkaUygulama();
        $uygulama = $model->tenantUygulama($id, $tenantId);

        if (!$uygulama || empty($uygulama['dokploy_application_id'])) {
            $_SESSION['error'] = 'Uygulama bulunamadı.';
            return $this->redirect('/uygulamalar');
        }

        try {
            (new EkaDokployServisi())->hamIstek('POST', '/application.delete', [
                'applicationId' => (string) $uygulama['dokploy_application_id'],
            ]);
            $model->delete($id);
            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), 'cloud_application_deleted', 'Uygulama silindi: ' . $uygulama['ad']);
            $_SESSION['success'] = 'Uygulama ve bağlı yerel kayıtları silindi.';
        } catch (Throwable $e) {
            $_SESSION['error'] = 'Uygulama silinemedi: ' . $e->getMessage();
        }

        return $this->redirect('/uygulamalar');
    }

    private function durumIslemi(string $islem)
    {
        $tenantId = (int) EkaTenant::id();
        $id = (int) ($_POST['id'] ?? 0);
        $model = new EkaUygulama();
        $uygulama = $model->tenantUygulama($id, $tenantId);

        if (!$uygulama || empty($uygulama['dokploy_application_id'])) {
            $_SESSION['error'] = 'Uygulama bulunamadı.';
            return $this->redirect('/uygulamalar');
        }

        try {
            (new EkaDokployServisi())->hamIstek('POST', '/application.' . $islem, [
                'applicationId' => (string) $uygulama['dokploy_application_id'],
            ]);

            $yeniDurum = $islem === 'start' ? 'running' : 'stopped';
            $model->update($id, [
                'durum' => $yeniDurum,
                'son_hata' => null,
                'last_sync_at' => date('Y-m-d H:i:s'),
            ]);

            (new EkaDagitimKaydi())->create([
                'tenant_id' => $tenantId,
                'uygulama_id' => $id,
                'islem' => $islem,
                'durum' => 'success',
                'mesaj' => $islem === 'start' ? 'Uygulama başlatıldı.' : 'Uygulama durduruldu.',
            ]);

            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), 'cloud_application_' . $islem, ($islem === 'start' ? 'Uygulama başlatıldı: ' : 'Uygulama durduruldu: ') . $uygulama['ad']);
            $_SESSION['success'] = $islem === 'start' ? 'Uygulama başlatıldı.' : 'Uygulama durduruldu.';
        } catch (Throwable $e) {
            $_SESSION['error'] = 'İşlem gerçekleştirilemedi: ' . $e->getMessage();
        }

        return $this->redirect('/uygulamalar');
    }
}