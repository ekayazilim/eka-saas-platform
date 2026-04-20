<?php

namespace App\Middlewares;

use Core\EkaRequest;
use Core\EkaResponse;

class EkaCsrfMiddleware
{
    public function handle(EkaRequest $request, EkaResponse $response): void
    {
        if ($request->getMethod() === 'post') {
            $token = $request->post('csrf_token');
            if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
                $response->setStatusCode(403);
                die('Güvenlik doğrulaması başarısız oldu. (CSRF)');
            }
        } else {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
    }
}
