<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

/**
 * SuccessLogins
 *
 * This model registers successfull logins registered users have made
 */
class SuccessLogins extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $usersId;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var string
     */
    public $userAgent;

    /**
     * get table name
     */

    public function getSource()
    {
        return 'user_success_logins';
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('usersId', '\Phasty\Common\Models\Users', 'id', ['alias' => 'user']);
    }

}