<?php

namespace App\Controllers;

use Core\EkaController;
use Core\EkaTenant;
use App\Models\EkaProject;
use App\Models\EkaActivityLog;
use Core\EkaAuth;

class EkaProjectController extends EkaController
{
    public function index()
    {
        $projectModel = new EkaProject();
        $projects = $projectModel->where('tenant_id', EkaTenant::id());
        
        return $this->view('projects/index', ['projects' => $projects]);
    }

    public function create()
    {
        return $this->view('projects/create');
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($name)) {
            $_SESSION['error'] = 'Proje adı zorunludur.';
            return $this->redirect('/projects/create');
        }

        $projectModel = new EkaProject();
        $projectId = $projectModel->create([
            'tenant_id' => EkaTenant::id(),
            'name' => $name,
            'description' => $description,
            'status' => 'active'
        ]);

        (new EkaActivityLog())->log(EkaTenant::id(), EkaAuth::id(), 'project_created', "Proje oluşturuldu: {$name}");

        $_SESSION['success'] = 'Proje başarıyla oluşturuldu.';
        return $this->redirect('/projects');
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $projectModel = new EkaProject();
        $project = $projectModel->find($id);

        if (!$project || $project['tenant_id'] !== EkaTenant::id()) {
            return $this->redirect('/projects');
        }

        return $this->view('projects/edit', ['project' => $project]);
    }

    public function update()
    {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'active';

        $projectModel = new EkaProject();
        $project = $projectModel->find($id);

        if ($project && $project['tenant_id'] === EkaTenant::id()) {
            $projectModel->update($id, [
                'name' => $name,
                'description' => $description,
                'status' => $status
            ]);
            
            (new EkaActivityLog())->log(EkaTenant::id(), EkaAuth::id(), 'project_updated', "Proje güncellendi: {$name}");
            $_SESSION['success'] = 'Proje başarıyla güncellendi.';
        }

        return $this->redirect('/projects');
    }
}
