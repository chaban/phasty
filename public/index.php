<?php
error_reporting(E_ALL);
ini_set('phalcon.orm.not_null_validations', 'Off');
//ini_set('zlib.output_compression', 'Off');
(new \Phalcon\Debug)->listen();
define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/vendor/autoload.php';
$config = include BASE_DIR . "/local/config.php";
$mailConfig = include BASE_DIR . "/local/mail.php";
include BASE_DIR . '/config/services.php';
//include BASE_DIR . '/config/bus_services.php';

/**
 * Handle the request
 */
$application = new \Phalcon\Mvc\Application();
$application->setDI($di);
/**
 * Register application modules
 */
$application->registerModules([
    'front' => [
        'className' => 'Phasty\Front\Module',
        'path' => BASE_DIR . '/app/Front/Module.php',
    ],
    'admin' => [
        'className' => 'Phasty\Admin\Module',
        'path' => BASE_DIR . '/app/Admin/Module.php',
    ]]);
echo $application->handle()->getContent();
