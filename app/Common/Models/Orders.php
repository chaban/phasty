<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;

class Orders extends Model
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
    public $userId;

    /**
     * @var string
     *
     */
    public $userName;

    /**
     * @var string
     *
     */
    public $email;

    /**
     * @var string
     *
     */
    public $phone;

    /**
     * @var string
     *
     */
    public $address;

    /**
     * @var string
     *
     */
    public $secretKey;

    /**
     * @var integer
     *
     */
    public $deliveryId;

    /**
     * @var string
     *
     */
    public $deliveryName;

    /**
     * @var integer
     *
     */
    public $statusId;

    /**
     * @var string
     *
     */
    public $statusName;

    /**
     * @var integer
     *
     */
    public $discountId;

    /**
     * @var string
     *
     */
    public $discountName;

    /**
     * @var double
     *
     */
    public $totalPrice;

    /**
     * @var double
     *
     */
    public $priceWithDelivery;

    /**
     * @var string
     *
     */
    public $ipAddress;

    /**
     * @var string
     *
     */
    public $createdAt;

    /**
     * @var string
     *
     */
    public $updatedAt;

    /**
     * @var string
     *
     */
    public $deletedAt;

    /**
     * @var string
     *
     */
    public $adminComment;

    /**
     * @var string
     *
     */
    public $userComment;

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'userId', 'email', 'phone', 'address', 'deliveryId', 'statusId',
            'discountId', 'userComment', 'adminComment'
        ];
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->hasMany('id', '\Phasty\Common\Models\OrderProducts', 'orderId', array('alias' =>
            'Products'));
        $this->belongsTo('statusId', '\Phasty\Common\Models\OrderStatus', 'id', array('alias' => 'Status'));
        $this->belongsTo('deliveryId', '\Phasty\Common\Models\Deliveries', 'id', array('alias' => 'Delivery'));
        $this->belongsTo('userId', '\Phasty\Common\Models\Users', 'id', array('alias' => 'User'));
    }

    public function beforeValidationOnCreate()
    {
        $this->secretKey = $this->createSecretKey();
        $this->createdAt = new RawValue('NOW()');
        $this->updatedAt = new RawValue('NOW()');
        if (!$this->statusId) {
            $this->statusId = 1;
        }
        if (!$this->discountId) {
            $this->discountId = null;
        }
        $this->ipAddress = $this->getDI()->getRequest()->getClientAddress();
    }

    public function beforeValidationOnUpdate()
    {
        $this->updatedAt = new RawValue('NOW()');
    }

    public function afterDelete()
    {
        //$this->getProducts()->delete();
    }

    public function createSecretKey($size = 40)
    {
        $result = '';
        $chars = '1234567890qweasdzxcrtyfghvbnuioplkjnmABCDEFGHAJKLMNOPQRSTUVWXYZ';
        while (mb_strlen($result, 'utf8') < $size) {
            $result .= mb_substr($chars, rand(0, mb_strlen($chars, 'utf8')), 1);
        }

        $result = md5($result);

        if (self::count(["secretKey = :secret_key:", "bind" => ['secret_key' => $result]]) > 0) {
            $this->createSecretKey($size);
        }

        return $result;
    }

    public function getDeliveryPrice()
    {
        $result = 0;

        if ($this->delivery->price > 0) {
            if ($this->delivery->freeFrom > 0 && $this->totalPrice > $this->delivery->freeFrom)
                $result = 0;
            else
                $result = $this->delivery->price;
        }
        return $result;
    }

    public function calculateTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->products as $product)
            $totalPrice += $product->price * $product->quantity;
        return $totalPrice;
    }


}
