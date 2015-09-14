<?php
$router = new \Phalcon\Mvc\Router();
$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
$router->setDefaultModule("front");
$router->removeExtraSlashes(true);
/**
 * Front routes
 */
$router->add('', array(
    'module' => 'front',
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => 'index',
    'action' => 'index'));

$router->add('/', array(
    'module' => "front",
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => "index",
    'action' => "index"))->setName('front-index');

$router->add('/index', array(
    'module' => "front",
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => "index",
    'action' => "index"))->setName('front-index');

$router->add('/index.html', array(
    'module' => "front",
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => "index",
    'action' => "index"))->setName('front-index');

$router->add('/:controller', array(
    'module' => "front",
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => 1,
    'action' => "index"))->setName('front-controller');

$router->add('/:controller/:action', array(
    'module' => "front",
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => 1,
    'action' => 2,
))->setName('front-action');

$router->add('/:controller/:action/:params', array(
    'module' => "front",
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => 1,
    'action' => 2,
    'params' => 3))->setName('front-full');

$router->add("/catalog/category/{slug:[a-zA-Z0-9_-]+}",
    array(
        'module' => 'front',
        'namespace' => 'Phasty\Front\Controllers\\',
        'controller' => 'catalog',
        'action' => 'index'));

$router->add("/catalog/category",
    array(
        'module' => 'front',
        'namespace' => 'Phasty\Front\Controllers\\',
        'controller' => 'catalog',
        'action' => 'index'));


$router->add('/session/confirm-email/{code}/{email}', array(
    'module' => "front",
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => 'session',
    'action' => 'confirmEmail'
));

$router->add('/session/reset-password/{code}/{email}', array(
    'module' => "front",
    'namespace' => 'Phasty\Front\Controllers\\',
    'controller' => 'session',
    'action' => 'resetPassword'
));


/**
 * Admin routes
 */

$router->add('/admin', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 'index',
    'action' => "index"
]);

$router->addGet('/admin/:controller', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 1,
    'action' => "index"
]);

$router->addGet('/admin/:controller/:int', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 1,
    'id' => 2,
    'action' => 'show',
]);

$router->addPost('/admin/:controller', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 1,
    'action' => 'store',
]);

$router->addPut('/admin/:controller/:int', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 1,
    'id' => 2,
    'action' => 'update',
]);

$router->addPatch('/admin/:controller/:int', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 1,
    'id' => 2,
    'action' => 'update',
]);

$router->addDelete('/admin/:controller/:int', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 1,
    'id' => 2,
    'action' => 'destroy',
]);

$router->addGet('/admin/dashboard', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 'index',
    'action' => "dashboard"
]);

$router->addGet('/admin/faker/fake', [
    'module' => "admin",
    'namespace' => 'Phasty\Admin\Controllers\\',
    'controller' => 'faker',
    'action' => 'fake',
]);


return $router;
