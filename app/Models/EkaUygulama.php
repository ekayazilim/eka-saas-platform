<?php

namespace App\Models;

use Core\EkaModel;
use PDO;

class EkaUygulama extends EkaModel
{
    protected string $table = 'uygulamalar';

    public function tenantUygulamalari(int $tenantId): array
    {
        $stmt = $this->db->prepare('SELECT u.*, p.name AS proje_adi FROM uygulamalar u INNER JOIN projects p ON p.id = u.project_id WHERE u.tenant_id = ? ORDER BY u.id DESC');
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tenantUygulama(int $id, int $tenantId): ?array
    {
        $stmt = $this->db->prepare('SELECT u.*, p.name AS proje_adi, p.dokploy_project_id, p.dokploy_environment_id FROM uygulamalar u INNER JOIN projects p ON p.id = u.project_id WHERE u.id = ? AND u.tenant_id = ? LIMIT 1');
        $stmt->execute([$id, $tenantId]);
        $sonuc = $stmt->fetch(PDO::FETCH_ASSOC);
        return $sonuc ?: null;
    }

    public function tenantSayisi(int $tenantId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM uygulamalar WHERE tenant_id = ?');
        $stmt->execute([$tenantId]);
        return (int) $stmt->fetchColumn();
    }
}