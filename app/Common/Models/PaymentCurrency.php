<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class PaymentCurrency extends Model
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
    public $currencyId;

    /**
     * @var integer
     *
     */
    public $paymentId;


}
