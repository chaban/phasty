<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

/**
 * PasswordChanges
 *
 * Register when a user changes his/her password
 */
class PasswordChanges extends Model
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
     * @var integer
     */
    public $createdAt;

    /**
     * get table name
     */

    public function getSource()
    {
        return 'user_password_changes';
    }

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        //Timestamp the confirmaton
        $this->createdAt = time();
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('usersId', 'Phasty\Common\Models\Users', 'id', ['alias' => 'user']);
    }

}