<?php namespace Phasty\Common\Commands;

class ConfirmUserEmailCommand {

    public $code;
    public $email;

    function __construct($code, $email)
    {
        $this->code = $code;
        $this->email = $email;
    }

}