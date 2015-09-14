<?php
/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new \Phalcon\DI\FactoryDefault();
$di->setShared('config', $config);

$loader = new \Phalcon\Loader();
$loader->registerNamespaces(array(
));

$loader->register();

$di->setShared('crypt', function () use ($config) {
    $crypt = new \Phalcon\Crypt();
    $crypt->setKey($config->app->cryptSalt);
    return $crypt;
});

$di->setShared('cookies', function () {
    $cookies = new \Phalcon\Http\Response\Cookies();
    return $cookies;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */

$di->setShared('db', function () use ($config) {
    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname,
        "charset" => $config->database->charset,
    ));
});

//Connect to redis
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);
$di->set('redis', $redis, true);

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () use ($config, $redis) {
    if (isset($config->models->metadata) && $config->models->metadata->adapter == 'Redis') {
        return new \Phalcon\Mvc\Model\MetaData\Redis(array(
            "lifetime" => 86400,
            "prefix"   => "phasty.metadata.",
            "redis"    => $redis
        ));;
    } else {
        return new \Phalcon\Mvc\Model\Metadata\Memory();
    }
});

/**
 * Cache
 */

$di->setShared('cache', function () use ($redis) {
    //Create a Data frontend and set a default lifetime to 1 hour
    $frontend = new \Phalcon\Cache\Frontend\Data(array(
        'lifetime' => 86400,
        'prefix' => 'phasty.cache.'
    ));

    //Create the cache passing the connection
    $cache = new \Phalcon\Cache\Backend\Redis($frontend, array(
        'redis' => $redis
    ));

    return $cache;
});

/**
 * Cache for Orm models
 */

$di->setShared('modelsCache', function () use($redis) {
    //Create a Data frontend and set a default lifetime to 1 hour
    $frontend = new \Phalcon\Cache\Frontend\Data(array(
        'lifetime' => 86400,
        'prefix' => 'phasty.modelsCache.'
    ));

    //Create the cache passing the connection
    $cache = new \Phalcon\Cache\Backend\Redis($frontend, array(
        'redis' => $redis
    ));

    return $cache;
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    //$session = new \Phalcon\Session\Adapter\Files();
    $session = new Phalcon\Session\Adapter\Redis(array(
        'path' => "tcp://127.0.0.1:6379?weight=1",
        'lifetime' => 86400
    ));
    $session->start();
    return $session;
});

/**
 * Registering a router
 */

$di->setShared('router', require BASE_DIR . '/config/routes.php');
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri('/');
    $url->setBasePath(BASE_DIR);
    return $url;
});

/**
 * We register the events manager
 */
$di->setShared('dispatcher', function () use ($di) {

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
 * Register the flash service with custom CSS classes
 */
$di->setShared('flash', function () {
    return new \Phalcon\Flash\Direct(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->setShared('flashSession', function () {
    return new \Phalcon\Flash\Session(array(
        'error' => 'alert alert-danger alert-dismissable',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
});

/**
 * Main logger file
 */
$di->setShared('logger', function () {
    return new \Phalcon\Logger\Adapter\File(BASE_DIR . '/var/logs/' . date('Y-m-d') . '.log');
});

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

$di->setShared('mailer', function () use ($config) {
    return new \Phalcon\Mailer\Manager($config->mail);
});

/**
 * user authentication
 */

$di->setShared('auth', function () {
    return new \Phasty\Common\Service\Auth\Auth();
});

/**
 * Helper functions
 */
$di->setShared('utils', function () {
    return new \utilphp\util();
});
