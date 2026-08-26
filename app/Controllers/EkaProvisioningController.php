<?php

namespace App\Controllers;

use App\Models\EkaActivityLog;
use App\Models\EkaPlan;
use App\Models\EkaTenantModel;
use App\Models\EkaUser;
use App\Models\EkaUygulama;
use App\Services\EkaDokployServisi;
use Core\EkaController;
use Core\EkaDatabase;
use Throwable;

class EkaProvisioningController extends EkaController
{
    public function musteriOlustur()
    {
        $this->yetkilendir();
        $girdi = $this->girdi();

        $firmaAdi = trim((string) ($girdi['company_name'] ?? ''));
        $ad = trim((string) ($girdi['name'] ?? ''));
        $email = mb_strtolower(trim((string) ($girdi['email'] ?? '')));
        $planId = (int) ($girdi['plan_id'] ?? 1);
        $sifre = (string) ($girdi['password'] ?? '');
        $otomatikSifre = false;

        if ($firmaAdi === '' || $ad === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'Firma adı, ad soyad ve geçerli e-posta zorunludur.'], 422);
        }

        if ($sifre === '') {
            $sifre = bin2hex(random_bytes(12));
            $otomatikSifre = true;
        }

        if (mb_strlen($sifre) < 12) {
            return $this->json(['success' => false, 'message' => 'Şifre en az 12 karakter olmalıdır.'], 422);
        }

        $plan = (new EkaPlan())->find($planId);
        if (!$plan) {
            return $this->json(['success' => false, 'message' => 'Paket bulunamadı.'], 422);
        }

        $userModel = new EkaUser();
        if ($userModel->first('email', $email)) {
            return $this->json(['success' => false, 'message' => 'Bu e-posta adresi zaten kayıtlı.'], 409);
        }

        $tenantModel = new EkaTenantModel();
        $slug = $this->benzersizSlug($tenantModel, $firmaAdi);
        $db = EkaDatabase::getConnection();

        try {
            $db->beginTransaction();

            $tenantId = $tenantModel->create([
                'name' => $firmaAdi,
                'slug' => $slug,
                'plan_id' => $planId,
                'status' => 'active',
            ]);

            $userId = $userModel->create([
                'tenant_id' => $tenantId,
                'name' => $ad,
                'email' => $email,
                'password' => password_hash($sifre, PASSWORD_DEFAULT),
                'role' => 'owner',
            ]);

            (new EkaActivityLog())->log((int) $tenantId, (int) $userId, 'provisioning_customer_created', 'Provisioning API üzerinden müşteri oluşturuldu.');
            $db->commit();

            $cevap = [
                'success' => true,
                'tenant_id' => (int) $tenantId,
                'user_id' => (int) $userId,
                'plan_id' => $planId,
                'slug' => $slug,
                'status' => 'active',
            ];

            if ($otomatikSifre) {
                $cevap['temporary_password'] = $sifre;
            }

            return $this->json($cevap, 201);
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            return $this->json(['success' => false, 'message' => 'Müşteri oluşturulamadı.'], 500);
        }
    }

    public function paketDegistir()
    {
        $this->yetkilendir();
        $girdi = $this->girdi();
        $tenant = $this->tenantBul($girdi);

        if (!$tenant) {
            return $this->json(['success' => false, 'message' => 'Müşteri bulunamadı.'], 404);
        }

        $planId = (int) ($girdi['plan_id'] ?? 0);
        $plan = (new EkaPlan())->find($planId);
        if (!$plan) {
            return $this->json(['success' => false, 'message' => 'Paket bulunamadı.'], 422);
        }

        (new EkaTenantModel())->update((int) $tenant['id'], ['plan_id' => $planId]);
        $uyarilar = $this->kaynakLimitleriniUygula((int) $tenant['id'], $plan);
        (new EkaActivityLog())->log((int) $tenant['id'], null, 'provisioning_plan_changed', 'Paket değiştirildi: #' . $planId);

        return $this->json([
            'success' => true,
            'tenant_id' => (int) $tenant['id'],
            'plan_id' => $planId,
            'warnings' => $uyarilar,
        ]);
    }

    public function askiyaAl()
    {
        $this->yetkilendir();
        $girdi = $this->girdi();
        $tenant = $this->tenantBul($girdi);

        if (!$tenant) {
            return $this->json(['success' => false, 'message' => 'Müşteri bulunamadı.'], 404);
        }

        $tenantId = (int) $tenant['id'];
        $uygulamaModel = new EkaUygulama();
        $uygulamalar = $uygulamaModel->tenantUygulamalari($tenantId);
        $dokploy = new EkaDokployServisi();
        $uyarilar = [];

        foreach ($uygulamalar as $uygulama) {
            if (($uygulama['durum'] ?? '') === 'suspended') {
                continue;
            }

            $oncekiDurum = (string) ($uygulama['durum'] ?? 'stopped');
            $dokployId = (string) ($uygulama['dokploy_application_id'] ?? '');

            if ($dokployId !== '' && $dokploy->hazirMi()) {
                try {
                    $dokploy->hamIstek('POST', '/application.stop', ['applicationId' => $dokployId]);
                } catch (Throwable $e) {
                    $uyarilar[] = $uygulama['ad'] . ': uzak servis durdurulamadı.';
                }
            }

            $uygulamaModel->update((int) $uygulama['id'], [
                'onceki_durum' => $oncekiDurum,
                'durum' => 'suspended',
                'last_sync_at' => date('Y-m-d H:i:s'),
            ]);
        }

        (new EkaTenantModel())->update($tenantId, ['status' => 'suspended']);
        (new EkaActivityLog())->log($tenantId, null, 'provisioning_customer_suspended', 'Müşteri ve uygulamaları askıya alındı.');

        return $this->json([
            'success' => true,
            'tenant_id' => $tenantId,
            'status' => 'suspended',
            'warnings' => $uyarilar,
        ]);
    }

    public function aktifEt()
    {
        $this->yetkilendir();
        $girdi = $this->girdi();
        $tenant = $this->tenantBul($girdi);

        if (!$tenant) {
            return $this->json(['success' => false, 'message' => 'Müşteri bulunamadı.'], 404);
        }

        $tenantId = (int) $tenant['id'];
        (new EkaTenantModel())->update($tenantId, ['status' => 'active']);

        $uygulamaModel = new EkaUygulama();
        $uygulamalar = $uygulamaModel->tenantUygulamalari($tenantId);
        $dokploy = new EkaDokployServisi();
        $uyarilar = [];

        foreach ($uygulamalar as $uygulama) {
            if (($uygulama['durum'] ?? '') !== 'suspended') {
                continue;
            }

            $oncekiDurum = (string) ($uygulama['onceki_durum'] ?? 'stopped');
            $dokployId = (string) ($uygulama['dokploy_application_id'] ?? '');
            $yeniDurum = in_array($oncekiDurum, ['running', 'ready', 'stopped', 'error'], true) ? $oncekiDurum : 'stopped';

            if ($oncekiDurum === 'running' && $dokployId !== '' && $dokploy->hazirMi()) {
                try {
                    $dokploy->hamIstek('POST', '/application.start', ['applicationId' => $dokployId]);
                    $yeniDurum = 'running';
                } catch (Throwable $e) {
                    $yeniDurum = 'stopped';
                    $uyarilar[] = $uygulama['ad'] . ': uzak servis yeniden başlatılamadı.';
                }
            }

            $uygulamaModel->update((int) $uygulama['id'], [
                'onceki_durum' => null,
                'durum' => $yeniDurum,
                'last_sync_at' => date('Y-m-d H:i:s'),
            ]);
        }

        (new EkaActivityLog())->log($tenantId, null, 'provisioning_customer_activated', 'Müşteri yeniden etkinleştirildi.');

        return $this->json([
            'success' => true,
            'tenant_id' => $tenantId,
            'status' => 'active',
            'warnings' => $uyarilar,
        ]);
    }

    private function yetkilendir(): void
    {
        $config = require CONFIG_PATH . '/provisioning.php';
        $beklenen = (string) ($config['api_key'] ?? '');
        $gelen = (string) ($_SERVER['HTTP_X_EKA_PROVISIONING_KEY'] ?? '');

        if ($beklenen === '') {
            $this->json(['success' => false, 'message' => 'Provisioning API yapılandırılmamış.'], 503);
        }

        if ($gelen === '' || !hash_equals($beklenen, $gelen)) {
            $this->json(['success' => false, 'message' => 'Yetkisiz istek.'], 401);
        }

        $izinliIpAdresleri = $config['allowed_ips'] ?? [];
        if ($izinliIpAdresleri) {
            $istemciIp = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
            if (!in_array($istemciIp, $izinliIpAdresleri, true)) {
                $this->json(['success' => false, 'message' => 'Bu IP adresinin erişim yetkisi yok.'], 403);
            }
        }
    }

    private function girdi(): array
    {
        $config = require CONFIG_PATH . '/provisioning.php';
        $maksimum = max(1024, (int) ($config['max_body_bytes'] ?? 65536));
        $ham = file_get_contents('php://input', false, null, 0, $maksimum + 1);

        if ($ham === false || strlen($ham) > $maksimum) {
            $this->json(['success' => false, 'message' => 'İstek gövdesi geçersiz veya çok büyük.'], 413);
        }

        $json = json_decode($ham, true);
        if (!is_array($json)) {
            $this->json(['success' => false, 'message' => 'Geçerli JSON gövdesi gönderilmelidir.'], 400);
        }

        return $json;
    }

    private function tenantBul(array $girdi): ?array
    {
        $model = new EkaTenantModel();
        $tenantId = (int) ($girdi['tenant_id'] ?? 0);

        if ($tenantId > 0) {
            $tenant = $model->find($tenantId);
            return $tenant ?: null;
        }

        $slug = trim((string) ($girdi['slug'] ?? ''));
        if ($slug !== '') {
            $tenant = $model->first('slug', $slug);
            return $tenant ?: null;
        }

        return null;
    }

    private function kaynakLimitleriniUygula(int $tenantId, array $plan): array
    {
        $uygulamalar = (new EkaUygulama())->tenantUygulamalari($tenantId);
        $dokploy = new EkaDokployServisi();
        $uyarilar = [];
        $cpu = max(0.1, ((int) ($plan['cpu_limit_millicores'] ?? 500)) / 1000);
        $cpuDegeri = rtrim(rtrim(number_format($cpu, 3, '.', ''), '0'), '.');
        $ramDegeri = ((int) ($plan['memory_limit_mb'] ?? 512)) . 'M';

        if (!$dokploy->hazirMi()) {
            return ['Dokploy bağlantısı yapılandırılmadığı için uzak kaynak limitleri uygulanamadı.'];
        }

        foreach ($uygulamalar as $uygulama) {
            $dokployId = (string) ($uygulama['dokploy_application_id'] ?? '');
            if ($dokployId === '') {
                continue;
            }

            try {
                $dokploy->uygulamaGuncelle($dokployId, [
                    'memoryLimit' => $ramDegeri,
                    'cpuLimit' => $cpuDegeri,
                ]);
            } catch (Throwable $e) {
                $uyarilar[] = $uygulama['ad'] . ': kaynak limiti uzak servise uygulanamadı.';
            }
        }

        return $uyarilar;
    }

    private function benzersizSlug(EkaTenantModel $model, string $firmaAdi): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $firmaAdi), '-'));
        if ($slug === '') {
            $slug = 'musteri-' . bin2hex(random_bytes(4));
        }

        $temel = $slug;
        $sayac = 2;

        while ($model->first('slug', $slug)) {
            $slug = $temel . '-' . $sayac;
            $sayac++;
        }

        return $slug;
    }
}