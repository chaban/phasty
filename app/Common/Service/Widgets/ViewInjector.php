<?php namespace Phasty\Common\Service\Widgets;

use Phalcon\Mvc\View\Simple;

class ViewInjector {
    protected $view;
    /**
     *
     */
    public function __construct(){
        $this->view = new Simple();
        $this->view->setViewsDir(__DIR__ . DIRECTORY_SEPARATOR . 'views'. DIRECTORY_SEPARATOR);
    }

    public function inject()
    {
        return $this->view;
    }
}