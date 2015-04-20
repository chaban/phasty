<?php
use Phasty\Common\Commands\RegisterUserCommand;
use Phasty\Common\Handlers\RegisterUserHandler;
//$di = \Phalcon\DI::getDefault();
/*$di->set('commandBus', function () {
    // Setup the bus, normally in your DI container ///////////////////////////////
    $locator = new \League\Tactician\Handler\Locator\InMemoryLocator([
        RegisterUserCommand::class => new RegisterUserHandler()
    ]);
    // Middleware is Tactician's plugin system. Even finding the handler and
    // executing it is a plugin that we're configuring here.
    $handlerMiddleware = new \League\Tactician\Handler\HandlerMiddleware(
        $locator,
        new \League\Tactician\Handler\MethodNameInflector\HandleInflector()
    );
    return new \League\Tactician\StandardCommandBus([new League\Tactician\Plugins\LockingMiddleware(),
        $handlerMiddleware]);
}); */

/*$di->set('commandBus', function(){
    return new stdClass();
});*/

$di->set('commandBus', function(){
    return new Phalcon\Commander\DefaultCommandBus(new Phalcon\Commander\BasicCommandTranslator());
});