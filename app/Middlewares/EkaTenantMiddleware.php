<?php

namespace App\Middlewares;

use App\Models\EkaTenantModel;
use Core\EkaRequest;
use Core\EkaResponse;
use Core\EkaTenant;

class EkaTenantMiddleware
{
    public function handle(EkaRequest $request, EkaResponse $response): void
    {
        $oturumTenant = EkaTenant::getCurrent();

        if (!$oturumTenant || empty($oturumTenant['id'])) {
            $_SESSION['error'] = 'Aktif bir organizasyon bulunamadı.';
            $response->redirect('/login');
        }

        $tenant = (new EkaTenantModel())->find((int) $oturumTenant['id']);

        if (!$tenant || ($tenant['status'] ?? '') !== 'active') {
            unset($_SESSION['user'], $_SESSION['tenant']);
            $_SESSION['error'] = 'Hesabınız askıya alınmış durumda. Hizmetlerinizi yeniden etkinleştirmek için destek ekibiyle iletişime geçiniz.';
            $response->redirect('/login');
        }

        EkaTenant::set($tenant);
    }
}