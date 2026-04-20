<?php

namespace App\Middlewares;

use Core\EkaAuth;
use Core\EkaRequest;
use Core\EkaResponse;

class EkaGuestMiddleware
{
    public function handle(EkaRequest $request, EkaResponse $response): void
    {
        if (EkaAuth::check()) {
            $user = EkaAuth::user();
            if (isset($user['role']) && $user['role'] === 'super_admin') {
                $response->redirect('/admin/dashboard');
            } else {
                $response->redirect('/dashboard');
            }
        }
    }
}
