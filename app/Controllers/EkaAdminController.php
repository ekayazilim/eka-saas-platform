<?php

namespace App\Controllers;

use Core\EkaController;
use App\Models\EkaTenantModel;
use App\Models\EkaUser;
use App\Models\EkaPlan;
use App\Models\EkaActivityLog;

class EkaAdminController extends EkaController
{
    public function dashboard()
    {
        $tenantModel = new EkaTenantModel();
        $userModel = new EkaUser();
        $logModel = new EkaActivityLog();

        $tenants = $tenantModel->all();
        $users = $userModel->all();
        $recentLogs = array_slice($logModel->all(), 0, 10);

        return $this->view('admin/dashboard', [
            'totalTenants' => count($tenants),
            'totalUsers' => count($users),
            'recentLogs' => $recentLogs
        ]);
    }

    public function tenants()
    {
        $tenantModel = new EkaTenantModel();
        
        $sql = "SELECT t.*, p.name as plan_name FROM tenants t LEFT JOIN plans p ON t.plan_id = p.id ORDER BY t.id DESC";
        $stmt = \Core\EkaDatabase::getConnection()->prepare($sql);
        $stmt->execute();
        $tenants = $stmt->fetchAll();

        return $this->view('admin/tenants/index', ['tenants' => $tenants]);
    }

    public function tenantCreate()
    {
        $planModel = new EkaPlan();
        $plans = $planModel->all();
        return $this->view('admin/tenants/create', ['plans' => $plans]);
    }

    public function tenantStore()
    {
        $tenantModel = new EkaTenantModel();
        $name = $_POST['name'] ?? '';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $plan_id = $_POST['plan_id'] ?? 1;
        $status = $_POST['status'] ?? 'active';

        $tenantModel->create([
            'name' => $name,
            'slug' => $slug,
            'plan_id' => $plan_id,
            'status' => $status
        ]);

        $_SESSION['success'] = 'Firma başarıyla eklendi.';
        return $this->redirect('/admin/tenants');
    }

    public function tenantEdit()
    {
        $id = $_GET['id'] ?? null;
        $tenantModel = new EkaTenantModel();
        $tenant = $tenantModel->find($id);

        if (!$tenant) {
            $_SESSION['error'] = 'Firma bulunamadı.';
            return $this->redirect('/admin/tenants');
        }

        $planModel = new EkaPlan();
        $plans = $planModel->all();

        return $this->view('admin/tenants/edit', ['tenant' => $tenant, 'plans' => $plans]);
    }

    public function tenantUpdate()
    {
        $id = $_POST['id'] ?? null;
        $tenantModel = new EkaTenantModel();
        
        $tenantModel->update($id, [
            'name' => $_POST['name'] ?? '',
            'plan_id' => $_POST['plan_id'] ?? 1,
            'status' => $_POST['status'] ?? 'active'
        ]);

        $_SESSION['success'] = 'Firma başarıyla güncellendi.';
        return $this->redirect('/admin/tenants');
    }

    public function tenantDelete()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $db = \Core\EkaDatabase::getConnection();
            $db->prepare("DELETE FROM activity_logs WHERE tenant_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM api_keys WHERE tenant_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM projects WHERE tenant_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM users WHERE tenant_id = ?")->execute([$id]);
            
            $tenantModel = new EkaTenantModel();
            $tenantModel->delete($id);
            $_SESSION['success'] = 'Firma ve ilişkili tüm veriler silindi.';
        }
        return $this->redirect('/admin/tenants');
    }
    
    public function tenantToggle()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $tenantModel = new EkaTenantModel();
            $tenant = $tenantModel->find($id);
            if ($tenant) {
                $newStatus = $tenant['status'] === 'active' ? 'suspended' : 'active';
                $tenantModel->update($id, ['status' => $newStatus]);
                $_SESSION['success'] = 'Firma durumu güncellendi.';
            }
        }
        return $this->redirect('/admin/tenants');
    }

    public function users()
    {
        $userModel = new EkaUser();
        return $this->view('admin/users/index', ['users' => $userModel->all()]);
    }

    public function userEdit()
    {
        $id = $_GET['id'] ?? null;
        $userModel = new EkaUser();
        $user = $userModel->find($id);

        if (!$user) {
            $_SESSION['error'] = 'Kullanıcı bulunamadı.';
            return $this->redirect('/admin/users');
        }

        $tenantModel = new EkaTenantModel();
        $tenants = $tenantModel->all();

        return $this->view('admin/users/edit', ['user' => $user, 'tenants' => $tenants]);
    }

    public function userUpdate()
    {
        $id = $_POST['id'] ?? null;
        $userModel = new EkaUser();

        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role' => $_POST['role'] ?? 'member',
            'tenant_id' => !empty($_POST['tenant_id']) ? $_POST['tenant_id'] : null
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);

        $_SESSION['success'] = 'Kullanıcı başarıyla güncellendi.';
        return $this->redirect('/admin/users');
    }

    public function userDelete()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $userModel = new EkaUser();
            
            if ($id == \Core\EkaAuth::id()) {
                $_SESSION['error'] = 'Kendi hesabınızı silemezsiniz.';
                return $this->redirect('/admin/users');
            }
            
            $userModel->delete($id);
            $_SESSION['success'] = 'Kullanıcı başarıyla silindi.';
        }
        return $this->redirect('/admin/users');
    }

    public function plans()
    {
        $planModel = new EkaPlan();
        return $this->view('admin/plans/index', ['plans' => $planModel->all()]);
    }

    public function planCreate()
    {
        return $this->view('admin/plans/create');
    }

    public function planStore()
    {
        $planModel = new EkaPlan();
        
        $planModel->create([
            'name' => $_POST['name'] ?? '',
            'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'] ?? ''))),
            'price' => $_POST['price'] ?? 0,
            'user_limit' => $_POST['user_limit'] ?? 1,
            'project_limit' => $_POST['project_limit'] ?? 1,
            'api_limit' => $_POST['api_limit'] ?? 1000
        ]);

        $_SESSION['success'] = 'Plan başarıyla eklendi.';
        return $this->redirect('/admin/plans');
    }

    public function planEdit()
    {
        $id = $_GET['id'] ?? null;
        $planModel = new EkaPlan();
        $plan = $planModel->find($id);

        if (!$plan) {
            $_SESSION['error'] = 'Plan bulunamadı.';
            return $this->redirect('/admin/plans');
        }

        return $this->view('admin/plans/edit', ['plan' => $plan]);
    }

    public function planUpdate()
    {
        $id = $_POST['id'] ?? null;
        $planModel = new EkaPlan();
        
        $planModel->update($id, [
            'name' => $_POST['name'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'user_limit' => $_POST['user_limit'] ?? 1,
            'project_limit' => $_POST['project_limit'] ?? 1,
            'api_limit' => $_POST['api_limit'] ?? 1000
        ]);

        $_SESSION['success'] = 'Plan başarıyla güncellendi.';
        return $this->redirect('/admin/plans');
    }

    public function planDelete()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $planModel = new EkaPlan();
            $planModel->delete($id);
            $_SESSION['success'] = 'Plan başarıyla silindi.';
        }
        return $this->redirect('/admin/plans');
    }

    public function logs()
    {
        $logModel = new EkaActivityLog();
        return $this->view('admin/logs/index', ['logs' => $logModel->all()]);
    }
}
