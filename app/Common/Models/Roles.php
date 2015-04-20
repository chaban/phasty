<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;

/**
 * Shop\Models\Profiles
 *
 * All the users registered in the application
 */
class Roles extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $active;

    /**
     * get table name
     */

    public function getSource()
    {
        return 'user_role';
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->hasMany('id', '\Shop\Models\Users', 'role_id',
            ['alias' => 'users', 'foreignKey' =>
                ['message' => 'Роль не может быть удалена, так как к ней относятся пользователи']]);

        $this->hasMany('id', '\Phasty\Common\Models\Permissions', 'role_id', ['alias' => 'permissions']);
    }

    public function beforeValidationOnCreate()
    {
        if (!$this->active) { // use default value if the value is not set
            $this->active = new RawValue('default');
        }
    }

}
