<?php
namespace Shop\Profile;

use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

    public function registerAutoloaders()
    {
        $loader = new \Phalcon\Loader();
        $loader->registerNamespaces(array(
            'Shop\Profile\Controllers' => __DIR__ . '/controllers/',
            'Shop\Forms' => __DIR__ . '/forms/'
        ));

        $loader->register();
    }

    public function registerServices($di)
    {

        /**
         * Setting up the view component
         */

        $di->set('view', function () {
                $view = new \Phalcon\Mvc\View();
                $view->setViewsDir(__DIR__ . '/views/');
                return $view;
            }
        );

    }
}
