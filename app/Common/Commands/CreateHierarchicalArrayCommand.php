<?php namespace Phasty\Common\Commands;

class CreateHierarchicalArrayCommand {

    public $arrayData;

    function __construct($arrayData)
    {
        $this->arrayData = $arrayData;
    }

}