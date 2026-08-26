<?php

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', BASE_PATH . '/core');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('VIEWS_PATH', APP_PATH . '/Views');

$appConfig = require CONFIG_PATH . '/app.php';
date_default_timezone_set($appConfig['timezone'] ?? 'Europe/Istanbul');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (bool) ($appConfig['session_secure_cookie'] ?? true),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$configUrl = (string) ($appConfig['url'] ?? '');
if ($configUrl !== '') {
    define('BASE_URL', $configUrl);
} else {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = preg_replace('/[^A-Za-z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    define('BASE_URL', $protocol . $host);
}

spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'Core\\')) {
        $relative = str_replace('Core\\', '', $class);
        $file = CORE_PATH . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    } elseif (str_starts_with($class, 'App\\')) {
        $relative = str_replace('App\\', '', $class);
        $file = APP_PATH . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    } else {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    }

    if (isset($file) && file_exists($file)) {
        require_once $file;
    }
});

require_once CONFIG_PATH . '/database.php';
require_once BASE_PATH . '/routes/web.php';

use Core\EkaRequest;
use Core\EkaResponse;
use Core\EkaRouter;

$request = new EkaRequest();
$response = new EkaResponse();
$router = EkaRouter::getInstance();

$router->dispatch($request, $response);