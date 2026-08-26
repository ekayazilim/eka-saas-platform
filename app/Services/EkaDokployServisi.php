<?php

namespace App\Services;

use RuntimeException;

class EkaDokployServisi
{
    private string $url;
    private string $apiKey;
    private string $serverId;
    private int $timeout;
    private bool $sslVerify;

    public function __construct()
    {
        $config = require CONFIG_PATH . '/dokploy.php';
        $this->url = $config['url'] ?? '';
        $this->apiKey = $config['api_key'] ?? '';
        $this->serverId = $config['server_id'] ?? '';
        $this->timeout = (int) ($config['timeout'] ?? 30);
        $this->sslVerify = (bool) ($config['ssl_verify'] ?? true);
    }

    public function hazirMi(): bool
    {
        return $this->url !== '' && $this->apiKey !== '';
    }

    public function varsayilanSunucuId(): ?string
    {
        return $this->serverId !== '' ? $this->serverId : null;
    }

    public function projeOlustur(string $ad, ?string $aciklama = null): array
    {
        return $this->istek('POST', '/project.create', [
            'name' => $ad,
            'description' => $aciklama,
        ]);
    }

    public function projeGetir(string $projeId): array
    {
        return $this->istek('GET', '/project.one', [
            'projectId' => $projeId,
        ]);
    }

    public function projeleriGetir(): array
    {
        return $this->istek('GET', '/project.all');
    }

    public function projeGuncelle(string $projeId, array $veri): array
    {
        return $this->istek('POST', '/project.update', array_merge([
            'projectId' => $projeId,
        ], $veri));
    }

    public function projeSil(string $projeId): array
    {
        return $this->istek('POST', '/project.remove', [
            'projectId' => $projeId,
        ]);
    }

    public function uygulamaOlustur(string $ad, string $ortamId, string $kaynakTipi = 'git', ?string $sunucuId = null, ?string $uygulamaAdi = null): array
    {
        $veri = [
            'name' => $ad,
            'environmentId' => $ortamId,
            'sourceType' => $kaynakTipi,
        ];

        if ($uygulamaAdi) {
            $veri['appName'] = $uygulamaAdi;
        }

        $atanacakSunucuId = $sunucuId ?: $this->varsayilanSunucuId();
        if ($atanacakSunucuId) {
            $veri['serverId'] = $atanacakSunucuId;
        }

        return $this->istek('POST', '/application.create', $veri);
    }

    public function githubKaydet(string $uygulamaId, string $sahip, string $repo, string $dal = 'main', string $githubId = '', string $buildYolu = '/'): array
    {
        return $this->istek('POST', '/application.saveGithubProvider', [
            'applicationId' => $uygulamaId,
            'repository' => $repo,
            'owner' => $sahip,
            'buildPath' => $buildYolu,
            'githubId' => $githubId,
            'branch' => $dal,
            'triggerType' => 'push',
            'enableSubmodules' => false,
            'watchPaths' => null,
        ]);
    }

    public function gitKaydet(string $uygulamaId, string $gitUrl, string $dal = 'main', string $buildYolu = '/'): array
    {
        return $this->istek('POST', '/application.saveGitProvider', [
            'applicationId' => $uygulamaId,
            'customGitBuildPath' => $buildYolu,
            'customGitUrl' => $gitUrl,
            'watchPaths' => null,
            'enableSubmodules' => false,
            'customGitBranch' => $dal,
            'customGitSSHKeyId' => null,
        ]);
    }

    public function dockerKaydet(string $uygulamaId, string $image, string $kullaniciAdi = '', string $sifre = '', string $registryUrl = ''): array
    {
        return $this->istek('POST', '/application.saveDockerProvider', [
            'applicationId' => $uygulamaId,
            'dockerImage' => $image,
            'username' => $kullaniciAdi,
            'password' => $sifre,
            'registryUrl' => $registryUrl,
        ]);
    }

    public function cevreDegiskenleriniKaydet(string $uygulamaId, string $env, string $buildArgs = '', string $buildSecrets = ''): array
    {
        return $this->istek('POST', '/application.saveEnvironment', [
            'applicationId' => $uygulamaId,
            'env' => $env,
            'buildArgs' => $buildArgs,
            'buildSecrets' => $buildSecrets,
            'createEnvFile' => true,
        ]);
    }

    public function buildTipiniKaydet(string $uygulamaId, string $buildTipi, ?string $publishDirectory = null, ?bool $staticSpa = null): array
    {
        $veri = [
            'applicationId' => $uygulamaId,
            'buildType' => $buildTipi,
            'dockerfile' => 'Dockerfile',
            'dockerContextPath' => '/',
            'dockerBuildStage' => '',
            'herokuVersion' => '24',
            'railpackVersion' => '0.15.4',
        ];

        if ($publishDirectory !== null) {
            $veri['publishDirectory'] = $publishDirectory;
        }

        if ($staticSpa !== null) {
            $veri['isStaticSpa'] = $staticSpa;
        }

        return $this->istek('POST', '/application.saveBuildType', $veri);
    }

    public function deployEt(string $uygulamaId, ?string $baslik = null, ?string $aciklama = null): array
    {
        $veri = ['applicationId' => $uygulamaId];

        if ($baslik !== null && $baslik !== '') {
            $veri['title'] = $baslik;
        }

        if ($aciklama !== null && $aciklama !== '') {
            $veri['description'] = $aciklama;
        }

        return $this->istek('POST', '/application.deploy', $veri);
    }

    public function yenidenDeployEt(string $uygulamaId): array
    {
        return $this->istek('POST', '/application.redeploy', [
            'applicationId' => $uygulamaId,
        ]);
    }

    public function uygulamaGuncelle(string $uygulamaId, array $veri): array
    {
        return $this->istek('POST', '/application.update', array_merge([
            'applicationId' => $uygulamaId,
        ], $veri));
    }

    public function hamIstek(string $yontem, string $yol, array $veri = []): array
    {
        return $this->istek($yontem, $yol, $veri);
    }

    private function istek(string $yontem, string $yol, array $veri = []): array
    {
        if (!$this->hazirMi()) {
            throw new RuntimeException('Dokploy bağlantısı yapılandırılmamış. DOKPLOY_URL ve DOKPLOY_API_KEY tanımlanmalıdır.');
        }

        if (!function_exists('curl_init')) {
            throw new RuntimeException('PHP cURL eklentisi etkin değil.');
        }

        $yontem = strtoupper($yontem);
        $adres = $this->url . '/api' . $yol;

        if ($yontem === 'GET' && $veri) {
            $adres .= '?' . http_build_query($veri);
        }

        $curl = curl_init($adres);
        $basliklar = [
            'Accept: application/json',
            'Content-Type: application/json',
            'x-api-key: ' . $this->apiKey,
        ];

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => min($this->timeout, 10),
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $basliklar,
            CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
            CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
        ]);

        if ($yontem !== 'GET') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $yontem);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($veri, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $cevap = curl_exec($curl);
        $hata = curl_error($curl);
        $durumKodu = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($cevap === false) {
            throw new RuntimeException('Dokploy API bağlantı hatası: ' . $hata);
        }

        $json = json_decode($cevap, true);
        if (!is_array($json)) {
            $json = ['raw' => $cevap];
        }

        if ($durumKodu < 200 || $durumKodu >= 300) {
            $mesaj = $json['message'] ?? $json['error'] ?? ('Dokploy API HTTP ' . $durumKodu);
            if (is_array($mesaj)) {
                $mesaj = json_encode($mesaj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            throw new RuntimeException((string) $mesaj, $durumKodu);
        }

        return $json;
    }
}