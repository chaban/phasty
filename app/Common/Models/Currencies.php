<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;
use Phalcon\Db\RawValue;

class Currencies extends Model
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var string
     *
     */
    public $name;

    /**
     * @var double
     *
     */
    public $rate;

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

    public function initialize(){
        $this->useDynamicUpdate(true);
    }

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'name', 'rate'
        ];
    }

    public function beforeValidationOnCreate(){
        $this->createdAt = new RawValue('now()');
        $this->updatedAt = new RawValue('now()');
    }

    public function beforeValidationOnUpdate(){
        $this->updatedAt = new RawValue('now()');
    }


}
