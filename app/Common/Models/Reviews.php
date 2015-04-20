<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;

/**
 * Shop\Models\Profiles
 *
 * All the users registered in the application
 */
class Reviews extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var int
     */
    public $productId;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $moderated;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * get table name
     */

    public function getSource()
    {
        return 'reviews';
    }

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'productId', 'userId', 'content', 'moderated'
        ];
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->hasOne('productId', 'Phasty\Common\Models\Products', 'id',
            ['alias' => 'Product']);
        $this->hasOne('userId', 'Phasty\Common\Models\Users', 'id',
            ['alias' => 'User']);
    }

    public function beforeValidationOnCreate()
    {
        if (!$this->moderated) { // use default value if the value is not set
            $this->moderated = new RawValue('default');
        }
        $this->createdAt = new RawValue('NOW()');
    }

}
