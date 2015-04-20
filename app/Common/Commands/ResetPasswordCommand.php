<?php namespace Phasty\Common\Commands;

class ResetPasswordCommand {

    public $email;

    function __construct($email)
    {
        $this->email = $email;
    }

}