<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

/**
 * ResetPasswords
 *
 * Stores the reset password codes and their evolution
 */
class ResetPasswords extends Model
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
    public $code;

    /**
     * @var integer
     */
    public $createdAt;

    /**
     * @var integer
     */
    public $modifiedAt;

    /**
     * @var string
     */
    public $reset;

    /**
     * get table name
     */

    public function getSource()
    {
        return 'user_reset_passwords';
    }

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        //Timestamp the confirmaton
        $this->createdAt = time();

        //Generate a random confirmation code
        $this->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        //Set status to non-confirmed
        $this->reset = 'N';
    }

    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        //Timestamp the confirmaton
        $this->modifiedAt = time();
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('usersId', '\Phasty\Common\Models\Users', 'id', ['alias' => 'user']);
    }

}