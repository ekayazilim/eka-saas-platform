<?php

namespace App\Controllers;

use App\Models\EkaPlan;
use Core\EkaController;

class EkaPlanYonetimController extends EkaController
{
    public function index()
    {
        return $this->view('admin/plans/index', ['plans' => (new EkaPlan())->all()]);
    }

    public function create()
    {
        return $this->view('admin/plans/create');
    }

    public function store()
    {
        $veri = $this->formVerisi();
        if ($veri === null) {
            return $this->redirect('/admin/plans/create');
        }

        $veri['slug'] = $this->slugOlustur($veri['name']);
        (new EkaPlan())->create($veri);
        $_SESSION['success'] = 'Developer Cloud paketi oluşturuldu.';
        return $this->redirect('/admin/plans');
    }

    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $plan = (new EkaPlan())->find($id);
        if (!$plan) {
            $_SESSION['error'] = 'Paket bulunamadı.';
            return $this->redirect('/admin/plans');
        }

        return $this->view('admin/plans/edit', ['plan' => $plan]);
    }

    public function update()
    {
        $id = (int) ($_POST['id'] ?? 0);
        $planModel = new EkaPlan();
        $plan = $planModel->find($id);
        if (!$plan) {
            $_SESSION['error'] = 'Paket bulunamadı.';
            return $this->redirect('/admin/plans');
        }

        $veri = $this->formVerisi();
        if ($veri === null) {
            return $this->redirect('/admin/plans/edit?id=' . $id);
        }

        $planModel->update($id, $veri);
        $_SESSION['success'] = 'Developer Cloud paketi güncellendi.';
        return $this->redirect('/admin/plans');
    }

    public function delete()
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            return $this->redirect('/admin/plans');
        }

        $db = \Core\EkaDatabase::getConnection();
        $stmt = $db->prepare('SELECT COUNT(*) FROM tenants WHERE plan_id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() > 0) {
            $_SESSION['error'] = 'Bu pakete bağlı müşteriler bulunduğu için paket silinemez.';
            return $this->redirect('/admin/plans');
        }

        (new EkaPlan())->delete($id);
        $_SESSION['success'] = 'Paket silindi.';
        return $this->redirect('/admin/plans');
    }

    private function formVerisi(): ?array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $price = max(0, (float) ($_POST['price'] ?? 0));
        $userLimit = max(1, (int) ($_POST['user_limit'] ?? 1));
        $projectLimit = max(1, (int) ($_POST['project_limit'] ?? 1));
        $applicationLimit = max(1, (int) ($_POST['application_limit'] ?? 1));
        $domainLimit = max(0, (int) ($_POST['domain_limit'] ?? 0));
        $databaseLimit = max(0, (int) ($_POST['database_limit'] ?? 0));
        $memoryLimit = max(128, (int) ($_POST['memory_limit_mb'] ?? 512));
        $cpuLimit = max(100, (int) ($_POST['cpu_limit_millicores'] ?? 500));
        $storageLimit = max(512, (int) ($_POST['storage_limit_mb'] ?? 5120));
        $apiLimit = max(0, (int) ($_POST['api_limit'] ?? 1000));

        if ($name === '') {
            $_SESSION['error'] = 'Paket adı zorunludur.';
            return null;
        }

        return [
            'name' => $name,
            'price' => $price,
            'user_limit' => $userLimit,
            'project_limit' => $projectLimit,
            'application_limit' => $applicationLimit,
            'domain_limit' => $domainLimit,
            'database_limit' => $databaseLimit,
            'memory_limit_mb' => $memoryLimit,
            'cpu_limit_millicores' => $cpuLimit,
            'storage_limit_mb' => $storageLimit,
            'allow_docker' => isset($_POST['allow_docker']) ? 1 : 0,
            'allow_databases' => isset($_POST['allow_databases']) ? 1 : 0,
            'allow_custom_domain' => isset($_POST['allow_custom_domain']) ? 1 : 0,
            'api_limit' => $apiLimit,
        ];
    }

    private function slugOlustur(string $name): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        if ($slug === '') {
            $slug = 'paket-' . bin2hex(random_bytes(4));
        }

        $planModel = new EkaPlan();
        $temel = $slug;
        $sayac = 2;
        while ($planModel->first('slug', $slug)) {
            $slug = $temel . '-' . $sayac;
            $sayac++;
        }

        return $slug;
    }
}