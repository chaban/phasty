<?php namespace Phasty\Common\Commands;

class SearchProductsCommand {

    public $brands;

    public $attributes;

    public $price;

    public $slug;

    public $page;

    function __construct($brands = [], $attributes = [], $price = [], $slug = '', $page = 1)
    {
        $this->brands = $brands;
        $this->attributes = $attributes;
        $this->price = $price;
        $this->slug = $slug;
        $this->page = $page;
    }

}