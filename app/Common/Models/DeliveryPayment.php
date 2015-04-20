<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class DeliveryPayment extends Model
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
    public $deliveryId;

    /**
     * @var integer
     *
     */
    public $paymentId;


}
