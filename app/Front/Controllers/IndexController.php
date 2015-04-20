<?php namespace Phasty\Front\Controllers;

use Phasty\Common\Models\Products;

class IndexController extends ControllerBase
{
    protected $products;

    protected function initialize()
    {
        $this->products = new Products();
        parent::initialize();
        $this->tag->setTitle('Home | E-Shopper');
    }

    /**
     * front page of site
     * @return view
     */
    public function indexAction()
    {
        //$this->modelsCache->flush();
        $products = $this->products->find(["order" => "rating desc", "limit" => 6,
            "cache" => ["key" => "products-on-index-page"]]);
        $this->view->setVars(['products' => $products]);
    }

    public function notFoundAction()
    {

    }

}
