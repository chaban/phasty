<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

/**
 * Permissions
 *
 * Stores the permissions by profile
 */
class Permissions extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $role_id;

    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     */
    public $action;

    /**
     * get table name
     */

    public function getSource()
    {
        return 'user_permissions';
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('role_id', '\Phasty\Common\Models\Roles', 'id', ['alias' => 'Role']);
    }

}