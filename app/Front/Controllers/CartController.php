<?php namespace Phasty\Front\Controllers;

use Phalcon\Commander\CommanderTrait;
use Phasty\Common\Models\Products;
use PhilipBrown\Basket\Converter;
use Phasty\Common\Commands\GenerateOrderCommand;


class CartController extends ControllerBase
{
    use CommanderTrait;

    protected function initialize()
    {
        parent::initialize();
        $this->tag->setTitle('Shopping cart | E-Shopper');
    }

    public function indexAction()
    {
        $this->view->setVars([
            'converter' => new Converter(),
            'order' => $this->getOrderAsArray()
        ]);
    }

    /**
     * add product to cart
     */

    public function addAction()
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            $id = $this->request->getPost('productId', 'int');
            $quantity = $this->request->getPost('quantity', 'int', 1);
            $model = Products::findFirstById($id);
            $discount = [];
            foreach ($model->brand->discount as $value) {
                if (isset($value->name) && isset($value->sum)) {
                    $discount = $value->toArray();
                    break;
                }
            }
            foreach ($model->category->discount as $value) {
                if (isset($value->name) && isset($value->sum)) {
                    $discount = $value->toArray();
                    break;
                }
            }
            if ($model) {
                $model->saveCounter('cart');

                $this->cart->insert([
                    'id' => $model->id,
                    'name' => $model->name,
                    'price' => $model->price,
                    'quantity' => $quantity,
                    'sku' => $model->sku,
                    'currency' => $this->session->has('currency') ? $this->session->get('currency') : $this->config->app->currency,
                    'options' => [
                        'image' => $model->getImages(),
                        'discount' => $discount,
                        'slug' => $model->slug
                    ]
                ]);
                echo 'success';
            }
        }
    }

    /**
     * remove product from cart
     */

    public function removeAction()
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            $id = $this->request->getPost('productId', 'int');
            $item = $this->cart->find($id);
            if ($item)
                $item->remove();
            $this->view->partial('cart/index', [
                'converter' => new Converter(),
                'order' => $this->getOrderAsArray()
            ]);
        }
    }

    /**
     * change quantity of the product in cart
     */

    public function quantityAction()
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            $quantity = $this->request->getPost('quantity', 'int');
            $id = $this->request->getPost('productId', 'int');
            $item = $this->cart->find($id);
            if ($item)
                $item->update('quantity', $quantity);
            $this->view->partial('cart/index', [
                'converter' => new Converter(),
                'order' => $this->getOrderAsArray()
            ]);
        }
    }

    protected function getOrderAsArray()
    {
        return $this->execute(GenerateOrderCommand::class, ['convert' => true]);
    }
}