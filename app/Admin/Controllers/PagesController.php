<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Pages\PhalconPages;
use Phasty\Common\Service\Form\PagesForm;
//use Phasty\Service\Transformer\PageTransformer;

class PagesController extends ControllerBase
{

    protected $repo;
    protected $form;

    protected function initialize()
    {
        $this->repo = new PhalconPages();
        $this->form = new PagesForm();
    }

}
