<?php namespace Phasty\Common\Commands;

class GenerateOrderCommand {

    public $convert;

    function __construct($convert = true)
    {
        $this->convert = $convert;
    }

}