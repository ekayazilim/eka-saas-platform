<?php

use App\Controllers\EkaAdminController;
use App\Controllers\EkaApiKeyController;
use App\Controllers\EkaAuthController;
use App\Controllers\EkaBillingController;
use App\Controllers\EkaDashboardController;
use App\Controllers\EkaDomainController;
use App\Controllers\EkaNotificationController;
use App\Controllers\EkaPlanYonetimController;
use App\Controllers\EkaProjectController;
use App\Controllers\EkaProvisioningController;
use App\Controllers\EkaUserController;
use App\Controllers\EkaUygulamaController;
use App\Controllers\EkaUygulamaIslemController;
use App\Middlewares\EkaAuthMiddleware;
use App\Middlewares\EkaCloudYonetimMiddleware;
use App\Middlewares\EkaGuestMiddleware;
use App\Middlewares\EkaRoleMiddleware;
use App\Middlewares\EkaTenantMiddleware;
use Core\EkaRouter;

$router = EkaRouter::getInstance();
$appConfig = require CONFIG_PATH . '/app.php';

$router->post('/api/provisioning/musteri-olustur', EkaProvisioningController::class, 'musteriOlustur');
$router->post('/api/provisioning/paket-degistir', EkaProvisioningController::class, 'paketDegistir');
$router->post('/api/provisioning/askiya-al', EkaProvisioningController::class, 'askiyaAl');
$router->post('/api/provisioning/aktif-et', EkaProvisioningController::class, 'aktifEt');

$router->middleware([App\Middlewares\EkaCsrfMiddleware::class], function () use ($router, $appConfig) {
    $router->get('/', function () {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }, 'index');

    $router->middleware([EkaGuestMiddleware::class], function () use ($router, $appConfig) {
        $router->get('/login', EkaAuthController::class, 'showLogin');
        $router->post('/login', EkaAuthController::class, 'login');
        if (!empty($appConfig['registration_enabled'])) {
            $router->get('/register', EkaAuthController::class, 'showRegister');
            $router->post('/register', EkaAuthController::class, 'register');
        }
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
                $router->get('/plans', EkaPlanYonetimController::class, 'index');
                $router->get('/plans/create', EkaPlanYonetimController::class, 'create');
                $router->post('/plans/store', EkaPlanYonetimController::class, 'store');
                $router->get('/plans/edit', EkaPlanYonetimController::class, 'edit');
                $router->post('/plans/update', EkaPlanYonetimController::class, 'update');
                $router->post('/plans/delete', EkaPlanYonetimController::class, 'delete');
                $router->get('/logs', EkaAdminController::class, 'logs');
            });
        });

        $router->middleware([EkaTenantMiddleware::class], function () use ($router) {
            $router->get('/dashboard', EkaDashboardController::class, 'index');
            $router->get('/projects', EkaProjectController::class, 'index');
            $router->get('/uygulamalar', EkaUygulamaController::class, 'index');
            $router->get('/uygulamalar/domainler', EkaDomainController::class, 'index');
            $router->get('/users', EkaUserController::class, 'index');
            $router->get('/billing', EkaBillingController::class, 'index');
            $router->get('/billing/plans', EkaBillingController::class, 'plans');
            $router->get('/api-keys', EkaApiKeyController::class, 'index');
            $router->get('/notifications', EkaNotificationController::class, 'index');
            $router->post('/notifications/read', EkaNotificationController::class, 'read');

            $router->middleware([EkaCloudYonetimMiddleware::class], function () use ($router) {
                $router->get('/projects/create', EkaProjectController::class, 'create');
                $router->post('/projects/store', EkaProjectController::class, 'store');
                $router->get('/projects/edit', EkaProjectController::class, 'edit');
                $router->post('/projects/update', EkaProjectController::class, 'update');
                $router->get('/uygulamalar/olustur', EkaUygulamaController::class, 'create');
                $router->post('/uygulamalar/kaydet', EkaUygulamaController::class, 'store');
                $router->post('/uygulamalar/deploy', EkaUygulamaController::class, 'deploy');
                $router->post('/uygulamalar/yeniden-deploy', EkaUygulamaController::class, 'redeploy');
                $router->post('/uygulamalar/baslat', EkaUygulamaIslemController::class, 'start');
                $router->post('/uygulamalar/durdur', EkaUygulamaIslemController::class, 'stop');
                $router->post('/uygulamalar/senkronize-et', EkaUygulamaIslemController::class, 'sync');
                $router->post('/uygulamalar/sil', EkaUygulamaIslemController::class, 'delete');
                $router->post('/uygulamalar/domainler/kaydet', EkaDomainController::class, 'store');
                $router->post('/uygulamalar/domainler/sil', EkaDomainController::class, 'delete');
                $router->get('/users/create', EkaUserController::class, 'create');
                $router->post('/users/store', EkaUserController::class, 'store');
                $router->post('/users/delete', EkaUserController::class, 'delete');
                $router->post('/api-keys/generate', EkaApiKeyController::class, 'generate');
                $router->post('/api-keys/revoke', EkaApiKeyController::class, 'revoke');
            });
        });
    });
});