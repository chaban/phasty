<?php namespace Phasty\Common\Service\Widgets;

use Phalcon\Widgets\AbstractWidget;

class TopRightMenu extends AbstractWidget {
    protected $view;

    /**
     * initialize variables
     */
    public function initialize(){
        $this->view = (new ViewInjector())->inject();
    }

    public function run()
    {
        echo $this->view->render('topRightMenu', ['controller' => $this->dispatcher->getControllerName()]);
    }
}