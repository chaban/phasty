<?php
namespace Phasty\Admin;

use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;

class Module implements ModuleDefinitionInterface {

	public function registerAutoloaders(DiInterface $di = null) {

		$loader = new Loader();
		$loader->registerNamespaces(array(
		));

		$loader->register();
	}

	public function registerServices(DiInterface $di = null) {

		/**
		 * Setting up the view component
		 */

		$di->set('view', function () {
			$view = new View();
			$view->setViewsDir(__DIR__ . '/views/');
			$view->setRenderLevel(View::LEVEL_NO_RENDER);
			$view->disable();
			return $view;
		});
	}
}
