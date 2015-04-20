<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class OrderProducts extends Model
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var integer
     *
     */
    public $orderId;

    /**
     * @var integer
     *
     */
    public $productId;

    /**
     * @var string
     *
     */
    public $name;

    /**
     * @var integer
     *
     */
    public $quantity;

    /**
     * @var string
     *
     */
    public $sku;

    /**
     * @var double
     *
     */
    public $price;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('order_id', '\Shop\Models\Order', 'id', array('alias' => 'Order'));
    }

    public function beforeCreate(){
        $product = \Shop\Models\Product::findFirst("id = $this->product_id");
        $product->decreaseQuantity();
    }

    public function afterSave()
    {
        $this->order->updateTotalPrice();
        $this->order->updateDeliveryPrice();
    }

    public function afterDelete()
    {
        if($this->order)
        {
            $this->order->updateTotalPrice();
            $this->order->updateDeliveryPrice();
        }
    }


}
