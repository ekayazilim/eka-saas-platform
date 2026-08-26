<?php

namespace App\Middlewares;

use Core\EkaRequest;
use Core\EkaResponse;
use Core\EkaTenant;

class EkaTenantMiddleware
{
    public function handle(EkaRequest $request, EkaResponse $response): void
    {
        $tenant = EkaTenant::getCurrent();

        if (!$tenant || empty($tenant['id'])) {
            $_SESSION['error'] = 'Aktif bir organizasyon bulunamadı.';
            $response->redirect('/login');
        }

        if (($tenant['status'] ?? 'active') !== 'active') {
            unset($_SESSION['user'], $_SESSION['tenant']);
            $_SESSION['error'] = 'Hesabınız askıya alınmış durumda. Hizmetlerinizi yeniden etkinleştirmek için destek ekibiyle iletişime geçiniz.';
            $response->redirect('/login');
        }
    }
}