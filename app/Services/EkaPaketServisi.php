<?php

namespace App\Services;

use App\Models\EkaPlan;
use App\Models\EkaProject;
use App\Models\EkaUygulama;
use Core\EkaTenant;
use RuntimeException;

class EkaPaketServisi
{
    public function aktifPaket(): array
    {
        $tenant = EkaTenant::getCurrent();
        if (!$tenant || empty($tenant['plan_id'])) {
            throw new RuntimeException('Aktif paket bulunamadı.');
        }

        $paket = (new EkaPlan())->find((int) $tenant['plan_id']);
        if (!$paket) {
            throw new RuntimeException('Paket kaydı bulunamadı.');
        }

        return $paket;
    }

    public function projeOlusturabilirMi(): bool
    {
        $tenantId = (int) EkaTenant::id();
        $paket = $this->aktifPaket();
        $limit = (int) ($paket['project_limit'] ?? 0);
        if ($limit <= 0) {
            return false;
        }

        $mevcut = count((new EkaProject())->where('tenant_id', $tenantId));
        return $mevcut < $limit;
    }

    public function uygulamaOlusturabilirMi(): bool
    {
        $tenantId = (int) EkaTenant::id();
        $paket = $this->aktifPaket();
        $limit = (int) ($paket['application_limit'] ?? 0);
        if ($limit <= 0) {
            return false;
        }

        return (new EkaUygulama())->tenantSayisi($tenantId) < $limit;
    }

    public function dockerKullanabilirMi(): bool
    {
        return (bool) ($this->aktifPaket()['allow_docker'] ?? false);
    }

    public function veritabaniKullanabilirMi(): bool
    {
        return (bool) ($this->aktifPaket()['allow_databases'] ?? false);
    }

    public function ozelDomainKullanabilirMi(): bool
    {
        return (bool) ($this->aktifPaket()['allow_custom_domain'] ?? false);
    }

    public function kaynakLimitleri(): array
    {
        $paket = $this->aktifPaket();

        return [
            'memory_limit_mb' => (int) ($paket['memory_limit_mb'] ?? 512),
            'cpu_limit_millicores' => (int) ($paket['cpu_limit_millicores'] ?? 500),
            'storage_limit_mb' => (int) ($paket['storage_limit_mb'] ?? 5120),
            'application_limit' => (int) ($paket['application_limit'] ?? 1),
            'project_limit' => (int) ($paket['project_limit'] ?? 1),
            'domain_limit' => (int) ($paket['domain_limit'] ?? 1),
            'database_limit' => (int) ($paket['database_limit'] ?? 0),
        ];
    }
}