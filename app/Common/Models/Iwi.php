<?php namespace Phasty\Common\Models;
use Phalcon\Mvc\Model;

/**
 * This is the model class for table "storage".
 *
 * The followings are the available columns in table 'storage':
 * @property string $key
 * @property string $value
 */
class Iwi extends Model
{
    /**
     * @var string
     *
     */
    public $key;

    /**
     * @var string
     *
     */
    public $value;


    /**
     * get table name
     */

    public function getSource()
    {
        return 'iwi';
    }
}