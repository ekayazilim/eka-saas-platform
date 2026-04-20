<?php

namespace App\Middlewares;

use Core\EkaTenant;
use Core\EkaRequest;
use Core\EkaResponse;

class EkaTenantMiddleware
{
    public function handle(EkaRequest $request, EkaResponse $response): void
    {
        if (!EkaTenant::id()) {
            $_SESSION['error'] = 'Aktif bir organizasyon bulunamadı.';
            $response->redirect('/login');
        }
    }
}
