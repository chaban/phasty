<?php namespace Phasty\Common\Commands;

class SendConfirmationEmailCommand
{
    public $userId;
    public $code;

    public function __construct($userId, $code)
    {
        $this->userId = $userId;
        $this->code = $code;
    }
}