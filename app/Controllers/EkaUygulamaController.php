<?php

namespace App\Controllers;

use App\Models\EkaActivityLog;
use App\Models\EkaDagitimKaydi;
use App\Models\EkaProject;
use App\Models\EkaUygulama;
use App\Services\EkaDokployServisi;
use App\Services\EkaPaketServisi;
use Core\EkaAuth;
use Core\EkaController;
use Core\EkaTenant;
use RuntimeException;
use Throwable;

class EkaUygulamaController extends EkaController
{
    public function index()
    {
        $tenantId = (int) EkaTenant::id();
        $uygulamalar = (new EkaUygulama())->tenantUygulamalari($tenantId);
        $paket = (new EkaPaketServisi())->aktifPaket();
        $limitler = (new EkaPaketServisi())->kaynakLimitleri();
        $dokployHazir = (new EkaDokployServisi())->hazirMi();

        return $this->view('uygulamalar/index', [
            'uygulamalar' => $uygulamalar,
            'paket' => $paket,
            'limitler' => $limitler,
            'dokployHazir' => $dokployHazir,
        ]);
    }

    public function create()
    {
        $tenantId = (int) EkaTenant::id();
        $projeler = (new EkaProject())->where('tenant_id', $tenantId);
        $paketServisi = new EkaPaketServisi();

        return $this->view('uygulamalar/olustur', [
            'projeler' => $projeler,
            'paket' => $paketServisi->aktifPaket(),
            'limitler' => $paketServisi->kaynakLimitleri(),
            'dockerKullanabilir' => $paketServisi->dockerKullanabilirMi(),
        ]);
    }

    public function store()
    {
        $tenantId = (int) EkaTenant::id();
        $ad = trim((string) ($_POST['ad'] ?? ''));
        $projectId = (int) ($_POST['project_id'] ?? 0);
        $platform = strtolower(trim((string) ($_POST['platform'] ?? 'react')));
        $kaynakTipi = strtolower(trim((string) ($_POST['kaynak_tipi'] ?? 'git')));
        $gitUrl = trim((string) ($_POST['git_url'] ?? ''));
        $gitSahip = trim((string) ($_POST['git_sahip'] ?? ''));
        $gitRepo = trim((string) ($_POST['git_repo'] ?? ''));
        $gitDal = trim((string) ($_POST['git_dal'] ?? 'main')) ?: 'main';
        $gitBuildYolu = trim((string) ($_POST['git_build_yolu'] ?? '/')) ?: '/';
        $githubId = trim((string) ($_POST['github_id'] ?? ''));
        $dockerImage = trim((string) ($_POST['docker_image'] ?? ''));
        $env = trim((string) ($_POST['env'] ?? ''));

        if ($ad === '' || $projectId <= 0) {
            $_SESSION['error'] = 'Uygulama adı ve proje seçimi zorunludur.';
            return $this->redirect('/uygulamalar/olustur');
        }

        $platformlar = ['react', 'nextjs', 'node', 'python', 'docker', 'static'];
        $kaynakTipleri = ['git', 'github', 'docker'];

        if (!in_array($platform, $platformlar, true) || !in_array($kaynakTipi, $kaynakTipleri, true)) {
            $_SESSION['error'] = 'Geçersiz platform veya kaynak tipi seçildi.';
            return $this->redirect('/uygulamalar/olustur');
        }

        $projectModel = new EkaProject();
        $proje = $projectModel->find($projectId);
        if (!$proje || (int) $proje['tenant_id'] !== $tenantId) {
            $_SESSION['error'] = 'Bu projeye erişim yetkiniz bulunmuyor.';
            return $this->redirect('/uygulamalar');
        }

        $paketServisi = new EkaPaketServisi();
        if (!$paketServisi->uygulamaOlusturabilirMi()) {
            $_SESSION['error'] = 'Paketinizin uygulama limiti doldu. Paket yükseltmeniz gerekmektedir.';
            return $this->redirect('/uygulamalar');
        }

        if (($platform === 'docker' || $kaynakTipi === 'docker') && !$paketServisi->dockerKullanabilirMi()) {
            $_SESSION['error'] = 'Mevcut paketiniz Docker uygulamalarına izin vermiyor.';
            return $this->redirect('/uygulamalar/olustur');
        }

        if ($kaynakTipi === 'git' && !filter_var($gitUrl, FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = 'Geçerli bir Git repository URL adresi giriniz.';
            return $this->redirect('/uygulamalar/olustur');
        }

        if ($kaynakTipi === 'github' && ($gitSahip === '' || $gitRepo === '' || $githubId === '')) {
            $_SESSION['error'] = 'GitHub kaynağı için sağlayıcı, repository sahibi ve repository adı zorunludur.';
            return $this->redirect('/uygulamalar/olustur');
        }

        if ($kaynakTipi === 'docker' && $dockerImage === '') {
            $_SESSION['error'] = 'Docker image adı zorunludur.';
            return $this->redirect('/uygulamalar/olustur');
        }

        $dokploy = new EkaDokployServisi();
        if (!$dokploy->hazirMi()) {
            $_SESSION['error'] = 'Deployment altyapısı henüz yapılandırılmamış.';
            return $this->redirect('/uygulamalar');
        }

        try {
            $proje = $this->dokployProjesiniHazirla($projectModel, $proje);
            $ortamId = (string) ($proje['dokploy_environment_id'] ?? '');
            if ($ortamId === '') {
                throw new RuntimeException('Dokploy environment kimliği oluşturulamadı.');
            }

            $uzakUygulama = $dokploy->uygulamaOlustur($ad, $ortamId, $kaynakTipi);
            $dokployUygulamaId = (string) ($uzakUygulama['applicationId'] ?? $uzakUygulama['application']['applicationId'] ?? '');
            $uygulamaAdi = (string) ($uzakUygulama['appName'] ?? $uzakUygulama['application']['appName'] ?? '');

            if ($dokployUygulamaId === '') {
                throw new RuntimeException('Dokploy uygulama kimliği alınamadı.');
            }

            if ($kaynakTipi === 'git') {
                $dokploy->gitKaydet($dokployUygulamaId, $gitUrl, $gitDal, $gitBuildYolu);
            } elseif ($kaynakTipi === 'github') {
                $dokploy->githubKaydet($dokployUygulamaId, $gitSahip, $gitRepo, $gitDal, $githubId, $gitBuildYolu);
            } else {
                $dokploy->dockerKaydet($dokployUygulamaId, $dockerImage);
            }

            if ($kaynakTipi !== 'docker') {
                if ($platform === 'react' || $platform === 'static') {
                    $dokploy->buildTipiniKaydet($dokployUygulamaId, 'static', 'dist', true);
                } elseif ($platform === 'docker') {
                    $dokploy->buildTipiniKaydet($dokployUygulamaId, 'dockerfile');
                } else {
                    $dokploy->buildTipiniKaydet($dokployUygulamaId, 'nixpacks');
                }
            }

            if ($env !== '') {
                $dokploy->cevreDegiskenleriniKaydet($dokployUygulamaId, $env);
            }

            $limitler = $paketServisi->kaynakLimitleri();
            $cpu = max(0.1, ((int) $limitler['cpu_limit_millicores']) / 1000);
            $dokploy->uygulamaGuncelle($dokployUygulamaId, [
                'memoryLimit' => ((int) $limitler['memory_limit_mb']) . 'M',
                'cpuLimit' => rtrim(rtrim(number_format($cpu, 3, '.', ''), '0'), '.'),
            ]);

            $uygulamaId = (new EkaUygulama())->create([
                'tenant_id' => $tenantId,
                'project_id' => $projectId,
                'ad' => $ad,
                'uygulama_adi' => $uygulamaAdi !== '' ? $uygulamaAdi : null,
                'platform' => $platform,
                'kaynak_tipi' => $kaynakTipi,
                'git_url' => $gitUrl !== '' ? $gitUrl : null,
                'git_sahip' => $gitSahip !== '' ? $gitSahip : null,
                'git_repo' => $gitRepo !== '' ? $gitRepo : null,
                'git_dal' => $gitDal,
                'git_build_yolu' => $gitBuildYolu,
                'github_id' => $githubId !== '' ? $githubId : null,
                'docker_image' => $dockerImage !== '' ? $dockerImage : null,
                'dokploy_application_id' => $dokployUygulamaId,
                'durum' => 'ready',
                'last_sync_at' => date('Y-m-d H:i:s'),
            ]);

            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), 'cloud_application_created', 'Uygulama oluşturuldu: ' . $ad . ' #' . $uygulamaId);
            $_SESSION['success'] = 'Uygulama oluşturuldu. İlk deployment işlemini başlatabilirsiniz.';
            return $this->redirect('/uygulamalar');
        } catch (Throwable $e) {
            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), 'cloud_application_create_failed', 'Uygulama oluşturma hatası: ' . $e->getMessage());
            $_SESSION['error'] = 'Uygulama oluşturulamadı: ' . $e->getMessage();
            return $this->redirect('/uygulamalar/olustur');
        }
    }

    public function deploy()
    {
        return $this->dagitimBaslat(false);
    }

    public function redeploy()
    {
        return $this->dagitimBaslat(true);
    }

    private function dagitimBaslat(bool $yeniden)
    {
        $tenantId = (int) EkaTenant::id();
        $id = (int) ($_POST['id'] ?? 0);
        $model = new EkaUygulama();
        $uygulama = $model->tenantUygulama($id, $tenantId);

        if (!$uygulama || empty($uygulama['dokploy_application_id'])) {
            $_SESSION['error'] = 'Uygulama bulunamadı veya deployment kaydı eksik.';
            return $this->redirect('/uygulamalar');
        }

        try {
            $dokploy = new EkaDokployServisi();
            $sonuc = $yeniden
                ? $dokploy->yenidenDeployEt((string) $uygulama['dokploy_application_id'])
                : $dokploy->deployEt((string) $uygulama['dokploy_application_id'], 'Eka Developer Cloud', $uygulama['ad']);

            $deploymentId = (string) ($sonuc['deploymentId'] ?? $sonuc['deployment']['deploymentId'] ?? '');
            $model->update($id, [
                'durum' => 'deploying',
                'son_hata' => null,
                'son_deploy_at' => date('Y-m-d H:i:s'),
                'last_sync_at' => date('Y-m-d H:i:s'),
            ]);

            (new EkaDagitimKaydi())->create([
                'tenant_id' => $tenantId,
                'uygulama_id' => $id,
                'dokploy_deployment_id' => $deploymentId !== '' ? $deploymentId : null,
                'islem' => $yeniden ? 'redeploy' : 'deploy',
                'durum' => 'queued',
                'mesaj' => $yeniden ? 'Yeniden deployment kuyruğa alındı.' : 'Deployment kuyruğa alındı.',
            ]);

            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), $yeniden ? 'cloud_application_redeploy' : 'cloud_application_deploy', 'Deployment başlatıldı: ' . $uygulama['ad']);
            $_SESSION['success'] = $yeniden ? 'Yeniden deployment başlatıldı.' : 'Deployment başlatıldı.';
        } catch (Throwable $e) {
            $model->update($id, [
                'durum' => 'error',
                'son_hata' => $e->getMessage(),
                'last_sync_at' => date('Y-m-d H:i:s'),
            ]);
            (new EkaActivityLog())->log($tenantId, EkaAuth::id(), 'cloud_application_deploy_failed', 'Deployment hatası: ' . $e->getMessage());
            $_SESSION['error'] = 'Deployment başlatılamadı: ' . $e->getMessage();
        }

        return $this->redirect('/uygulamalar');
    }

    private function dokployProjesiniHazirla(EkaProject $projectModel, array $proje): array
    {
        if (!empty($proje['dokploy_project_id']) && !empty($proje['dokploy_environment_id'])) {
            return $proje;
        }

        $dokploy = new EkaDokployServisi();
        $uzak = $dokploy->projeOlustur((string) $proje['name'], $proje['description'] ?? null);
        $dokployProjeId = (string) ($uzak['project']['projectId'] ?? $uzak['projectId'] ?? '');
        $dokployOrtamId = (string) ($uzak['environment']['environmentId'] ?? $uzak['environmentId'] ?? '');

        if ($dokployProjeId === '' || $dokployOrtamId === '') {
            throw new RuntimeException('Dokploy proje veya environment kimliği alınamadı.');
        }

        $projectModel->update((int) $proje['id'], [
            'dokploy_project_id' => $dokployProjeId,
            'dokploy_environment_id' => $dokployOrtamId,
            'provision_status' => 'ready',
            'provision_error' => null,
            'last_sync_at' => date('Y-m-d H:i:s'),
        ]);

        $proje['dokploy_project_id'] = $dokployProjeId;
        $proje['dokploy_environment_id'] = $dokployOrtamId;
        $proje['provision_status'] = 'ready';
        return $proje;
    }
}