<?php
namespace Phasty\Front;

use Phalcon\Cart\Cart;
use Phalcon\Cart\Identifier\Cookie;
use Phalcon\Cart\Storage\Session;
use Phalcon\Iwi\Iwi;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;
use Phalcon\Widgets\WidgetFactory;

class Module implements ModuleDefinitionInterface
{

    public function registerAutoloaders()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
        ));

        $loader->register();
    }

    public function registerServices($di)
    {
        /**
         * Setting up the view component
         */

        $di->set('view', function () {
                $view = new View();
                $view->setViewsDir(__DIR__ . '/views/');
                $view->setTemplateAfter('main');
                return $view;
            }
        );

        /**
         * Images resize and cache
         */
        $di->set('iwi', function () {
            return new Iwi();
        });

        /**
         * cart
         */
        $di->set('cart', function(){
            return new Cart(new Session(), new Cookie());
        });

        /**
         * views widgets, $namespace were app widgets is located
         */
        $di->set('widgets', function(){
            return new WidgetFactory($namespace = '\Phasty\Common\Service\Widgets');
        });
    }
}
