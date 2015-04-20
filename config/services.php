<?php
/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new \Phalcon\DI\FactoryDefault();
$di->set('config', $config);
$di->set('mailConfig', $mailConfig);

$loader = new \Phalcon\Loader();
$loader->registerNamespaces(array(
//'Phalcon' => __DIR__ . '/../vendor/phalcon/incubator/Library/Phalcon',
    //'Shop\Components\YandexParser' => __DIR__ . '/../../common/library/YandexParser/',
));

$loader->register();

$di->set('crypt', function () use ($config) {
    $crypt = new \Phalcon\Crypt();
    $crypt->setKey($config->app->cryptSalt);
    return $crypt;
});

$di->set('cookies', function () {
    $cookies = new \Phalcon\Http\Response\Cookies();
    return $cookies;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */

$di->set('db', function () use ($config) {
    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname,
        "charset" => $config->database->charset,
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () use ($config) {
    if (isset($config->models->metadata)) {
        $metadataAdapter = 'Phalcon\Mvc\Model\Metadata\\' . $config->models->metadata->adapter;
        return new $metadataAdapter();
    } else {
        return new \Phalcon\Mvc\Model\Metadata\Memory();
    }
});

/**
 * Cache for Orm models
 */

$di->set('modelsCache', function () {

    //Connect to redis
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);

    //Create a Data frontend and set a default lifetime to 1 hour
    $frontend = new Phalcon\Cache\Frontend\Data(array(
        'lifetime' => 86400
    ));

    //Create the cache passing the connection
    $cache = new \Phalcon\Cache\Backend\Redis($frontend, array(
        'redis' => $redis
    ));

    return $cache;
});

/**
 * Registering a router
 */

$di->set('router', require BASE_DIR . '/config/routes.php');
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () {
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri('/');
    $url->setBasePath(BASE_DIR);
    return $url;
});

/**
 * We register the events manager
 */
$di->set('dispatcher', function () use ($di) {

    $eventsManager = $di->getShared('eventsManager');
    /**
     * if not found page
     */
    $eventsManager->attach("dispatch", function ($event, $dispatcher, $exception) {

        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND :
                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND :
                    $dispatcher->
                    forward(array(
                        'module' => 'frontend',
                        'namespace' => 'Phasty\Front\Controllers\\',
                        'controller' => 'index',
                        'action' => 'notFound'));
                    return false;
            }
        }
    }
    );
    $dispatcher = new \Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    //$session = new \Phalcon\Session\Adapter\Files();
    $session = new Phalcon\Session\Adapter\Redis(array(
        'path' => "tcp://127.0.0.1:6379?weight=1",
        'lifetime' => 86400
    ));
    $session->start();
    return $session;
});

/**
 * Register the flash service with custom CSS classes
 */
$di->set('flash', function () {
    return new \Phalcon\Flash\Direct(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flashSession', function () {
    return new \Phalcon\Flash\Session(array(
        'error' => 'alert alert-danger alert-dismissable',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
});

/**
 * Main logger file
 */
$di->set('logger', function () {
    return new \Phalcon\Logger\Adapter\File(__DIR__ . '/../var/logs/' . date('Y-m-d') . '.log');
}, true);

/**
 * Error handler
 */
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($di) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    $di->getFlash()->error($errstr);
    $di->getLogger()->log($errstr . ' ' . $errfile . ':' . $errline, \Phalcon\Logger::ERROR);
    return true;
});

/**
 * application command bus
 */

$di->set('commandBus', function () {
    return new Phalcon\Commander\DefaultCommandBus(new Phalcon\Commander\BasicCommandTranslator());
});

/**
 * mail sending service
 */

$di->set('mailer', function () use ($mailConfig) {
    return new \Phalcon\Mailer\Manager($mailConfig->mail);
});

/**
 * mail sending service
 */

$di->set('auth', function () {
    return new \Phasty\Common\Auth\Auth();
});

/**
 * Helper functions
 */
$di->set('utils', function () {
    return new \utilphp\util();
});
