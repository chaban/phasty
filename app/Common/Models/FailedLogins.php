<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

/**
 * FailedLogins
 *
 * This model registers unsuccessfull logins registered and non-registered users have made
 */
class FailedLogins extends Model
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
     * @var integer
     */
    public $attempted;

    public function getSource()
    {
        return 'user_failed_logins';
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('usersId', 'Phasty\Common\Models\Users', 'id', array(
            'alias' => 'user'
        ));
    }

}