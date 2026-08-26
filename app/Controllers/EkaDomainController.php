<?php

namespace App\Controllers;

use App\Models\EkaActivityLog;
use App\Models\EkaUygulama;
use App\Models\EkaUygulamaDomaini;
use App\Services\EkaDokployServisi;
use App\Services\EkaPaketServisi;
use Core\EkaAuth;
use Core\EkaController;
use Core\EkaTenant;
use Throwable;

class EkaDomainController extends EkaController
{
    public function index()
    {
        $tenantId = (int) EkaTenant::id();
        $uygulamaId = (int) ($_GET['id'] ?? 0);
        $uygulama = (new EkaUygulama())->tenantUygulama($uygulamaId, $tenantId);

        if (!$uygulama) {
            $_SESSION['error'] = 'Uygulama bulunamadı.';
            return $this->redirect('/uygulamalar');
        }

        $model = new EkaUygulamaDomaini();
        $limitler = (new EkaPaketServisi())->kaynakLimitleri();

        return $this->view('domainler/index', [
            'uygulama' => $uygulama,
            'domainler' => $model->uygulamaDomainleri($uygulamaId, $tenantId),
            'domainSayisi' => $model->tenantSayisi($tenantId),
            'domainLimiti' => (int) $limitler['domain_limit'],
            'varsayilanPort' => $this->varsayilanPort((string) $uygulama['platform']),
            'ozelDomainKullanabilir' => (new EkaPaketServisi())->ozelDomainKullanabilirMi(),
        ]);
    }

    public function store()
    {
        $tenantId = (int) EkaTenant::id();
        $uygulamaId = (int) ($_POST['uygulama_id'] ?? 0);
        $host = $this->hostTemizle((string) ($_POST['host'] ?? ''));
        $port = (int) ($_POST['port'] ?? 0);
        $https = isset($_POST['https']);
        $uygulama = (new EkaUygulama())->tenantUygulama($uygulamaId, $tenantId);

        if (!$uygulama || empty($uygulama['dokploy_application_id'])) {
            $_SESSION['error'] = 'Uygulama bulunamadı veya deployment kaydı eksik.';
            return $this->redirect('/uygulamalar');
        }

        $paketServisi = new EkaPaketServisi();
        if (!$paketServisi->ozelDomainKullanabilirMi()) {
            $_SESSION['error'] = 'Mevcut paketiniz özel domain kullanımına izin vermiyor.';
            return $this->redirect('/uygulamalar/domainler?id=' . $uygulamaId);
        }

        $domainModel = new EkaUygulamaDomaini();
        $limit = (int) $paketServisi->kaynakLimitleri()['domain_limit'];
        if ($limit <= 0 || $domainModel->tenantSayisi($tenantId) >= $limit) {
            $_SESSION['error'] = 'Paketinizin domain limiti doldu.';
            return $this->redirect('/uygulamalar/domainler?id=' . $uygulamaId);
        }

        if (!$this->gecerliHost($host)) {
            $_SESSION['error'] = 'Geçerli bir domain adı giriniz.';
            return $this->redirect('/uygulamalar/domainler?id=' . $uygulamaId);
        }

        if ($port < 1 || $port > 65535) {
            $_SESSION['error'] = 'Container port değeri 1 ile 65535 arasında olmalıdır.';
            return $this->redirect('/uygulamalar/domainler?id=' . $uygulamaId);
        }

        if ($domainModel->first('host', $host)) {
            $_SESSION['error'] = 'Bu domain platformda zaten kayıtlı.';
            return $this->redirect('/uygulamalar/domainler?id=' . $uygulamaId);
        }

        try {
            $dokploy = new EkaDokployServisi();
            $uzak = $dokploy->hamIstek('POST', '/domain.create', [
                'host' => $host,
                'path' => '/',
                'port' => $port,
                'https' => $https,
                'applicationId' => (string) $uygulama['dokploy_application_id'],
                'certificateType' => $https ? 'letsencrypt' : 'none',
                'domainType' => 'application',
                'internalPath' => '/',
                'stripPath' => false,
                'forwardAuthEnabled' => false,
            ]);

            $dokployDomainId = (string) ($uzak['domainId'] ?? $uzak['domain']['domainId'] ?? '');
            if ($dokployDomainId === '') {
                throw new \RuntimeException('Dokploy domain kimliği alınamadı.');
            }

            $domainModel->create([
                'tenant_id' => $tenantId,
                'uygulama_id' => $uygulamaId,
                'host' => $host,
                'port' => $port,
                'https' => $https ? 1 : 0,
                'certificate_type' => $https ? 'letsencrypt' : 'none',
                'dokploy_domain_id' => $dokployDomainId,
            ]);

            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), 'cloud_domain_created', 'Domain eklendi: ' . $host);
            $_SESSION['success'] = 'Domain eklendi ve reverse proxy yapılandırması oluşturuldu.';
        } catch (Throwable $e) {
            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), 'cloud_domain_create_failed', 'Domain ekleme hatası: ' . $e->getMessage());
            $_SESSION['error'] = 'Domain eklenemedi: ' . $e->getMessage();
        }

        return $this->redirect('/uygulamalar/domainler?id=' . $uygulamaId);
    }

    public function delete()
    {
        $tenantId = (int) EkaTenant::id();
        $id = (int) ($_POST['id'] ?? 0);
        $domainModel = new EkaUygulamaDomaini();
        $domain = $domainModel->tenantDomain($id, $tenantId);

        if (!$domain) {
            $_SESSION['error'] = 'Domain bulunamadı.';
            return $this->redirect('/uygulamalar');
        }

        $uygulamaId = (int) $domain['uygulama_id'];

        try {
            (new EkaDokployServisi())->hamIstek('POST', '/domain.delete', [
                'domainId' => (string) $domain['dokploy_domain_id'],
            ]);
            $domainModel->delete($id);
            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), 'cloud_domain_deleted', 'Domain silindi: ' . $domain['host']);
            $_SESSION['success'] = 'Domain kaldırıldı.';
        } catch (Throwable $e) {
            $_SESSION['error'] = 'Domain kaldırılamadı: ' . $e->getMessage();
        }

        return $this->redirect('/uygulamalar/domainler?id=' . $uygulamaId);
    }

    private function varsayilanPort(string $platform): int
    {
        return match ($platform) {
            'react', 'static' => 80,
            'python' => 8000,
            'docker' => 80,
            default => 3000,
        };
    }

    private function hostTemizle(string $host): string
    {
        $host = strtolower(trim($host));
        $host = preg_replace('#^https?://#i', '', $host) ?? $host;
        $host = explode('/', $host)[0];
        return trim($host, '.');
    }

    private function gecerliHost(string $host): bool
    {
        if ($host === '' || strlen($host) > 253 || !str_contains($host, '.')) {
            return false;
        }

        return (bool) preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $host);
    }
}