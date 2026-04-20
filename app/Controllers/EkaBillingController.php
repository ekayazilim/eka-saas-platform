<?php

namespace App\Controllers;

use Core\EkaController;
use Core\EkaTenant;
use App\Models\EkaPlan;
use App\Models\EkaInvoice;

class EkaBillingController extends EkaController
{
    public function index()
    {
        $tenantId = EkaTenant::id();
        $tenant = EkaTenant::getCurrent();
        $planModel = new EkaPlan();
        $invoiceModel = new EkaInvoice();

        $currentPlan = $planModel->find($tenant['plan_id']);
        $invoices = $invoiceModel->where('tenant_id', $tenantId);

        return $this->view('billing/index', [
            'currentPlan' => $currentPlan,
            'invoices' => $invoices
        ]);
    }

    public function plans()
    {
        $planModel = new EkaPlan();
        $plans = $planModel->all();
        
        return $this->view('billing/plans', [
            'plans' => $plans
        ]);
    }
}
