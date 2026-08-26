<?php

namespace App\Middlewares;

use Core\EkaAuth;
use Core\EkaRequest;
use Core\EkaResponse;

class EkaCloudYonetimMiddleware
{
    public function handle(EkaRequest $request, EkaResponse $response): void
    {
        $kullanici = EkaAuth::user();
        $rol = (string) ($kullanici['role'] ?? 'member');

        if (!in_array($rol, ['owner', 'admin'], true)) {
            $_SESSION['error'] = 'Bu işlem için yönetici yetkisi gereklidir.';
            $response->redirect('/uygulamalar');
        }
    }
}