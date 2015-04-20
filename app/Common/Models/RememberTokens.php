<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

/**
 * RememberTokens
 *
 * Stores the remember me tokens
 */
class RememberTokens extends Model
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
    public $token;

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
        return 'user_remember_tokens';
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