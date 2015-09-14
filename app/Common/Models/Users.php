<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;
use Phasty\Common\Commands\CreateEmailConfirmationCommand;

/**
 * Phasty\Common\Models\Users
 *
 * All the users registered in the application
 */
class Users extends Model
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
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $mustChangePassword;

    /**
     * @var string
     */
    public $confirmed;

    /**
     * @var string
     */
    public $banned;

    /**
     * @var string
     */
    public $role;

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'id', 'name', 'email', 'password', 'mustChangePassword', 'confirmed', 'banned', 'role'
        ];
    }

    /**
     * get table name
     */

    public function getSource()
    {
        return 'users';
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);

        /*$this->belongsTo('role_id', '\Phasty\Common\Models\Roles', 'id',
            ['alias' => 'Role', 'reusable' => true]);*/

        $this->hasOne('id', 'Phasty\Common\Models\UserProfile', 'userId',
            ['alias' => 'Profile']);

        $this->hasMany('id', '\Phasty\Common\Models\SuccessLogins', 'usersId',
            ['alias' => 'successLogins', 'foreignKey' => ['message' =>
                'User cannot be deleted because he/she has activity in the system']
            ]);

        $this->hasMany('id', '\Phasty\Common\Models\PasswordChanges', 'usersId',
            ['alias' => 'passwordChanges', 'foreignKey' =>
                ['message' => 'User cannot be deleted because he/she has activity in the system']
            ]);

        $this->hasMany('id', '\Phasty\Common\Models\ResetPasswords', 'usersId',
            ['alias' => 'resetPasswords',
                'foreignKey' =>
                    ['message' => 'User cannot be deleted because he/she has activity in the system']
            ]);

        $this->hasMany('id', '\Phasty\Common\Models\Reviews', 'userId',
            ['alias' => 'reviews',
                'foreignKey' =>
                    ['message' => 'User cannot be deleted because he/she has activity in the system']
            ]);
    }

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        if (empty($this->password)) {

            //Generate a plain temporary password
            $tempPassword = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));

            //The user must change its password in first login
            $this->mustChangePassword = 'Y';
            $this->confirmed = 'N';

            //Use this password as default
            $this->password = $this->getDI()->getSecurity()->hash($tempPassword);

        } else {
            //The user must not change its password in first login
            $this->mustChangePassword = 'N';

        }

        //The user not banned on default
        if (empty($this->banned)) {
            $this->banned = 'N';
        }

        //The account must be confirmed via e-mail
        if (empty($this->confirmed)) {
            $this->confirmed = 'N';
        }

        if(!$this->role){
            $this->role = new RawValue('default');
        }
    }

    /**
     * Send a confirmation e-mail to the user if the account is not active
     */
    public function afterCreate()
    {
        if ($this->confirmed == 'N') {
            $this->getDI()->getCommandBus()->execute(new CreateEmailConfirmationCommand($this->id));
        }
    }

}
