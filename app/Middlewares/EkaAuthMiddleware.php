<?php

namespace App\Middlewares;

use Core\EkaAuth;
use Core\EkaRequest;
use Core\EkaResponse;

class EkaAuthMiddleware
{
    public function handle(EkaRequest $request, EkaResponse $response): void
    {
        if (!EkaAuth::check()) {
            $_SESSION['error'] = 'Lütfen giriş yapın.';
            $response->redirect('/login');
        }
    }
}
