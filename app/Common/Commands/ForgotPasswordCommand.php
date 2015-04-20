<?php namespace Phasty\Common\Commands;

class ForgotPasswordCommand {

    public $email;

    function __construct($email)
    {
        $this->email = $email;
    }

}