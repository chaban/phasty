<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;

class OrderStatus extends Model
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
     * @var integer
     *
     */
    public $position;

    public function beforeValidationOnCreate()
    {
        if(!$this->position)
        {
            $this->position = new RawValue('default');
        }
    }


}
