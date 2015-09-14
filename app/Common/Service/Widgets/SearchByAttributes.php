<?php namespace Phasty\Common\Service\Widgets;

use Phalcon\Widgets\AbstractWidget;

class SearchByAttributes extends AbstractWidget {
    protected $category; //current category, initialized by parent abstract class
    protected $view; // simple views service for widgets

    /**
     * initialize variables
     */
    public function initialize(){
        $this->view = (new ViewInjector())->inject();
    }

    public function run()
    {
        echo $this->view->render('searchByAttributes', ['category' => $this->category]);
    }
}