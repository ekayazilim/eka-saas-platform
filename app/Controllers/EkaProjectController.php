<?php

namespace App\Controllers;

use App\Models\EkaActivityLog;
use App\Models\EkaProject;
use App\Services\EkaPaketServisi;
use Core\EkaAuth;
use Core\EkaController;
use Core\EkaTenant;

class EkaProjectController extends EkaController
{
    public function index()
    {
        $projectModel = new EkaProject();
        $projects = $projectModel->where('tenant_id', EkaTenant::id());
        $paketServisi = new EkaPaketServisi();

        return $this->view('projects/index', [
            'projects' => $projects,
            'limitler' => $paketServisi->kaynakLimitleri(),
        ]);
    }

    public function create()
    {
        $paketServisi = new EkaPaketServisi();
        if (!$paketServisi->projeOlusturabilirMi()) {
            $_SESSION['error'] = 'Paketinizin proje limiti doldu. Yeni proje için paket yükseltmeniz gerekmektedir.';
            return $this->redirect('/projects');
        }

        return $this->view('projects/create');
    }

    public function store()
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($name === '') {
            $_SESSION['error'] = 'Proje adı zorunludur.';
            return $this->redirect('/projects/create');
        }

        $paketServisi = new EkaPaketServisi();
        if (!$paketServisi->projeOlusturabilirMi()) {
            $_SESSION['error'] = 'Paketinizin proje limiti doldu. Yeni proje için paket yükseltmeniz gerekmektedir.';
            return $this->redirect('/projects');
        }

        $projectModel = new EkaProject();
        $projectId = $projectModel->create([
            'tenant_id' => EkaTenant::id(),
            'name' => $name,
            'description' => $description !== '' ? $description : null,
            'status' => 'active',
            'provision_status' => 'pending',
        ]);

        (new EkaActivityLog())->log(EkaTenant::id(), EkaAuth::id(), 'project_created', 'Proje oluşturuldu: ' . $name . ' #' . $projectId);

        $_SESSION['success'] = 'Proje oluşturuldu. İlk uygulama eklenirken deployment ortamı otomatik hazırlanacaktır.';
        return $this->redirect('/projects');
    }

    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $projectModel = new EkaProject();
        $project = $projectModel->find($id);

        if (!$project || (int) $project['tenant_id'] !== (int) EkaTenant::id()) {
            return $this->redirect('/projects');
        }

        return $this->view('projects/edit', ['project' => $project]);
    }

    public function update()
    {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $status = (string) ($_POST['status'] ?? 'active');

        if ($name === '' || !in_array($status, ['active', 'archived'], true)) {
            $_SESSION['error'] = 'Proje bilgileri geçersiz.';
            return $this->redirect('/projects');
        }

        $projectModel = new EkaProject();
        $project = $projectModel->find($id);

        if ($project && (int) $project['tenant_id'] === (int) EkaTenant::id()) {
            $projectModel->update($id, [
                'name' => $name,
                'description' => $description !== '' ? $description : null,
                'status' => $status,
            ]);

            (new EkaActivityLog())->log(EkaTenant::id(), EkaAuth::id(), 'project_updated', 'Proje güncellendi: ' . $name);
            $_SESSION['success'] = 'Proje başarıyla güncellendi.';
        }

        return $this->redirect('/projects');
    }
}