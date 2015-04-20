<?php namespace Phasty\Front\Controllers;


use Phasty\Common\Models\Products;

class ProductsController extends ControllerBase
{
    protected function initialize()
    {
        parent::initialize();
        $this->tag->setTitle('Product | E-Shopper');
    }

    /**
     * just forward to catalog
     */
    public function indexAction()
    {
        $this->dispatcher->forward([
            'controller' => 'catalog',
            'view' => 'index'
        ]);
    }

    public function showAction($slug = '')
    {
        $product = Products::findFirstBySlug($slug);
        if(!$product){
            $this->flashSession->error('There is no such a product');
            return $this->response->redirect('catalog');
        }
        $this->view->setVars([
            'product' => $product,
            'images' => $product->getImages(true),
            'reviews' => $product->reviews ? $product->reviews : null
        ]);
    }

}