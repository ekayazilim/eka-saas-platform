<?php

namespace App\Models;

use Core\EkaModel;
use PDO;

class EkaUygulamaDomaini extends EkaModel
{
    protected string $table = 'uygulama_domainleri';

    public function uygulamaDomainleri(int $uygulamaId, int $tenantId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM uygulama_domainleri WHERE uygulama_id = ? AND tenant_id = ? ORDER BY id DESC');
        $stmt->execute([$uygulamaId, $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tenantDomain(int $id, int $tenantId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM uygulama_domainleri WHERE id = ? AND tenant_id = ? LIMIT 1');
        $stmt->execute([$id, $tenantId]);
        $sonuc = $stmt->fetch(PDO::FETCH_ASSOC);
        return $sonuc ?: null;
    }

    public function tenantSayisi(int $tenantId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM uygulama_domainleri WHERE tenant_id = ?');
        $stmt->execute([$tenantId]);
        return (int) $stmt->fetchColumn();
    }
}