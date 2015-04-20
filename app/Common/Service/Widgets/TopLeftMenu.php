<?php namespace Phasty\Common\Service\Widgets;

use Phalcon\Widgets\AbstractWidget;

class TopLeftMenu extends AbstractWidget {
	protected $view;

	/**
	 * initialize variables
	 */
	public function initialize() {
		$this->view = (new ViewInjector())->inject();
	}

	public function run() {
		echo $this->view->render('topLeftMenu', ['controller' =>
			$this->dispatcher->getControllerName(), 'action' => $this->dispatcher->getActionName()]);
	}
}
