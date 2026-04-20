<?php

namespace App\Controllers;

use Core\EkaController;
use Core\EkaAuth;
use Core\EkaTenant;
use App\Models\EkaUser;
use App\Models\EkaProject;
use App\Models\EkaApiKey;
use App\Models\EkaActivityLog;

class EkaDashboardController extends EkaController
{
    public function index()
    {
        $user = EkaAuth::user();
        if ($user['role'] === 'super_admin') {
            return $this->redirect('/admin/dashboard');
        }

        $tenantId = EkaTenant::id();
        
        $userModel = new EkaUser();
        $projectModel = new EkaProject();
        $apiKeyModel = new EkaApiKey();
        $logModel = new EkaActivityLog();

        $totalUsers = count($userModel->where('tenant_id', $tenantId));
        $totalProjects = count($projectModel->where('tenant_id', $tenantId));
        $apiKeys = $apiKeyModel->where('tenant_id', $tenantId);
        $totalKeys = count($apiKeys);
        
        $recentProjects = array_slice($projectModel->where('tenant_id', $tenantId), 0, 5);
        
        // Find Plan
        $tenantModel = new \App\Models\EkaTenantModel();
        $tenant = $tenantModel->find($tenantId);
        $planModel = new \App\Models\EkaPlan();
        $plan = $planModel->find($tenant['plan_id'] ?? 1);

        return $this->view('dashboard/index', [
            'totalUsers' => $totalUsers,
            'totalProjects' => $totalProjects,
            'totalKeys' => $totalKeys,
            'recentProjects' => $recentProjects,
            'plan' => $plan
        ]);
    }
}
