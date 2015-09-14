<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;

class Deliveries extends Model
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var double
     *
     */
    public $price;

    /**
     * @var double
     *
     */
    public $freeFrom;

    /**
     * @var integer
     *
     */
    public $position;

    /**
     * @var integer
     *
     */
    public $active;

    /**
     * @var string
     *
     */
    public $name;

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'name', 'active', 'price', 'freeFrom', 'position'
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
            '\Phasty\Common\Models\DeliveryPayment',
            'deliveryId', 'paymentId',
            '\Phasty\Common\Models\Payments',
            'id',
            ['alias' => 'Payments'/*),
                "foreignKey" => [
                    "message" => "The category cannot be deleted because some products are using it"]*/]
        );
    }

    public function beforeValidationOnCreate()
    {
        if (!$this->position) {
            $this->position = new RawValue('default');
        }
        if(!$this->active){
            $this->active = new RawValue('default');
        }
    }
}
