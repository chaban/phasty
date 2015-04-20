<?php namespace Phasty\Common\Commands;

class CreateEmailConfirmationCommand {

    public $userId;

    function __construct($id)
    {
        $this->userId = $id;
    }

}