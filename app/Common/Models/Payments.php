<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;
use utilphp\util;

class Payments extends Model
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
    public $active;

    /**
     * @var string
     *
     */
    public $paymentSystem;

    /**
     * @var integer
     *
     */
    public $position;

    /**
     * @var string
     */
    public $name;

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'name', 'active', 'paymentSystem', 'position'
        ];
    }

    /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->hasManyToMany(
            'id',
            '\Phasty\Common\Models\PaymentCurrency',
            'paymentId', 'currencyId',
            '\Phasty\Common\Models\Currencies',
            'id',
            ['alias' => 'Currencies'/*),
                "foreignKey" => [
                    "message" => "The category cannot be deleted because some products are using it"]*/]
        );
    }

    public function beforeValidation(){
        $this->paymentSystem = util::slugify($this->name);
    }


}
