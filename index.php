<?php

session_start();

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', BASE_PATH . '/core');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('VIEWS_PATH', APP_PATH . '/Views');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . $host);

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

require_once CONFIG_PATH . '/app.php';
require_once CONFIG_PATH . '/database.php';
require_once BASE_PATH . '/routes/web.php';

use Core\EkaRouter;
use Core\EkaRequest;
use Core\EkaResponse;

$request = new EkaRequest();
$response = new EkaResponse();
$router = EkaRouter::getInstance();

$router->dispatch($request, $response);