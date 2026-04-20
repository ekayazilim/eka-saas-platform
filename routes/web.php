<?php

use Core\EkaRouter;
use App\Controllers\EkaAuthController;
use App\Controllers\EkaDashboardController;
use App\Controllers\EkaProjectController;
use App\Controllers\EkaUserController;
use App\Controllers\EkaBillingController;
use App\Controllers\EkaApiKeyController;
use App\Controllers\EkaNotificationController;
use App\Controllers\EkaAdminController;
use App\Middlewares\EkaAuthMiddleware;
use App\Middlewares\EkaGuestMiddleware;
use App\Middlewares\EkaRoleMiddleware;
use App\Middlewares\EkaTenantMiddleware;

$router = EkaRouter::getInstance();

$router->middleware([App\Middlewares\EkaCsrfMiddleware::class], function () use ($router) {
    $router->get('/', function () {
        header("Location: " . BASE_URL . "/login");
        exit;
    }, 'index');

    $router->middleware([EkaGuestMiddleware::class], function () use ($router) {
        $router->get('/login', EkaAuthController::class, 'showLogin');
        $router->post('/login', EkaAuthController::class, 'login');
        $router->get('/register', EkaAuthController::class, 'showRegister');
        $router->post('/register', EkaAuthController::class, 'register');
        $router->get('/forgot-password', EkaAuthController::class, 'showForgot');
    });

    $router->get('/logout', EkaAuthController::class, 'logout');

    $router->middleware([EkaAuthMiddleware::class], function () use ($router) {

        $router->group('/admin', function () use ($router) {
            $router->middleware([EkaRoleMiddleware::class], function () use ($router) {
                $router->get('/dashboard', EkaAdminController::class, 'dashboard');
                
                $router->get('/tenants', EkaAdminController::class, 'tenants');
                $router->get('/tenants/create', EkaAdminController::class, 'tenantCreate');
                $router->post('/tenants/store', EkaAdminController::class, 'tenantStore');
                $router->get('/tenants/edit', EkaAdminController::class, 'tenantEdit');
                $router->post('/tenants/update', EkaAdminController::class, 'tenantUpdate');
                $router->post('/tenants/delete', EkaAdminController::class, 'tenantDelete');
                $router->post('/tenants/toggle', EkaAdminController::class, 'tenantToggle');
                
                $router->get('/users', EkaAdminController::class, 'users');
                $router->get('/users/edit', EkaAdminController::class, 'userEdit');
                $router->post('/users/update', EkaAdminController::class, 'userUpdate');
                $router->post('/users/delete', EkaAdminController::class, 'userDelete');
                
                $router->get('/plans', EkaAdminController::class, 'plans');
                $router->get('/plans/create', EkaAdminController::class, 'planCreate');
                $router->post('/plans/store', EkaAdminController::class, 'planStore');
                $router->get('/plans/edit', EkaAdminController::class, 'planEdit');
                $router->post('/plans/update', EkaAdminController::class, 'planUpdate');
                $router->post('/plans/delete', EkaAdminController::class, 'planDelete');
                
                $router->get('/logs', EkaAdminController::class, 'logs');
            });
        });

        $router->middleware([EkaTenantMiddleware::class], function () use ($router) {
            $router->get('/dashboard', EkaDashboardController::class, 'index');

            $router->get('/projects', EkaProjectController::class, 'index');
            $router->get('/projects/create', EkaProjectController::class, 'create');
            $router->post('/projects/store', EkaProjectController::class, 'store');
            $router->get('/projects/edit', EkaProjectController::class, 'edit');
            $router->post('/projects/update', EkaProjectController::class, 'update');

            $router->get('/users', EkaUserController::class, 'index');
            $router->get('/users/create', EkaUserController::class, 'create');
            $router->post('/users/store', EkaUserController::class, 'store');
            $router->post('/users/delete', EkaUserController::class, 'delete');

            $router->get('/billing', EkaBillingController::class, 'index');
            $router->get('/billing/plans', EkaBillingController::class, 'plans');

            $router->get('/api-keys', EkaApiKeyController::class, 'index');
            $router->post('/api-keys/generate', EkaApiKeyController::class, 'generate');
            $router->post('/api-keys/revoke', EkaApiKeyController::class, 'revoke');

            $router->get('/notifications', EkaNotificationController::class, 'index');
            $router->post('/notifications/read', EkaNotificationController::class, 'read');
        });
    });
});
