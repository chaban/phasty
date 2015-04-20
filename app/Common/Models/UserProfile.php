<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;

class UserProfile extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     *
     * @var string
     */
    public $address;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var integer
     */
    public $commentsCount;

    /**
     *
     * @var string
     */
    public $createdAt;

    /**
     *
     * @var string
     */
    public $lastLogin;

    /**
     *
     * @var string
     */
    public $wishList;

    /**
     *
     * @var string
     */
    public $compareList;

    /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('userId', 'Phasty\Common\Models\Users', 'id', ['alias' => 'User']);
    }

    public function beforeValidationOnCreate(){
        $this->createdAt  = new RawValue('now()');
        $this->commentsCount = new RawValue('default');
    }

    public function beforeSave()
    {
        $this->lastLogin = new RawValue('default');
    }

}
