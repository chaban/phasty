<?php namespace Phasty\Common\Service\Basket\TaxRates;

use PhilipBrown\Basket\TaxRate;

/**
 * !!!!!!! WARNING THIS IS NOT ACTUAL DATA - THIS IS FOR DEVELOPMENT DUMMY DATA
 * Class BelarusValueAddedTax
 * @package Phalcon\Basket\TaxRates
 */
class BelarusValueAddedTax implements TaxRate
{
    /**
     * @var float
     */
    private $rate;

    /**
     * Create a new Tax Rate
     */
    public function __construct()
    {
        $this->rate = 0.87;
    }

    /**
     * Return the Tax Rate as a float
     *
     * @return float
     */
    public function float()
    {
        return $this->rate;
    }

    /**
     * Return the Tax Rate as a percentage
     *
     * @return int
     */
    public function percentage()
    {
        return intval($this->rate * 100);
    }
}