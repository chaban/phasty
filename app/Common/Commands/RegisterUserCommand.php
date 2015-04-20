<?php namespace Phasty\Common\Commands;

class RegisterUserCommand {

    public $name;

    public $email;

    public $password;

    function __construct($name, $email, $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

} 