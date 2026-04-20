<?php

namespace App\Middlewares;

use Core\EkaAuth;
use Core\EkaRequest;
use Core\EkaResponse;

class EkaRoleMiddleware
{
    public function handle(EkaRequest $request, EkaResponse $response): void
    {
        $user = EkaAuth::user();
        if (!$user) {
            $response->redirect('/login');
        }

        if (isset($user['role']) && $user['role'] !== 'super_admin') {
            $_SESSION['error'] = 'Bu alana erişim yetkiniz yok.';
            $response->redirect('/dashboard');
        }
    }
}
