<?php namespace Phasty\Common\Commands;

class CompareWishCommand {

    public $productId;

    public $what;

    public $action;

    public $profile;

    function __construct($productId = null, $what = '', $action = '', $profile = null)
    {
        $this->productId = $productId;
        $this->what = $what;
        $this->action = $action;
        $this->profile = $profile;
    }

}