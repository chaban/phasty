<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;
use Phasty\Common\Commands\SendConfirmationEmailCommand;

/**
 * EmailConfirmations
 *
 * Stores the reset password codes and their evolution
 */
class EmailConfirmations extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $userId;

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
    public $confirmed;

    /**
     * get table name
     */

    public function getSource()
    {
        return 'user_email_confirmations';
    }

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        //Timestamp the confirmaton
        $this->createdAt = time();
        $this->modifiedAt = time();

        //Generate a random confirmation code
        $this->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        //Set status to non-confirmed
        $this->confirmed = 'N';
    }

    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        //Timestamp the confirmation
        $this->modifiedAt = time();
    }

    /**
     * Send a confirmation e-mail to the user after create the account
     */
    public function afterCreate()
    {

        $this->getDI()->getCommandBus()->execute(new SendConfirmationEmailCommand($this->userId, $this->code));
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('userId', '\Phasty\Common\Models\Users', 'id', ['alias' => 'user']);
    }

}