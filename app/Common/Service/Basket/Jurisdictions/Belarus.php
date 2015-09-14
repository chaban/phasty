<?php namespace Phasty\Common\Service\Basket\Jurisdictions;

use Money\Currency;
use Phalcon\Mvc\User\Plugin;
use PhilipBrown\Basket\Jurisdiction;
use Phasty\Common\Service\Basket\TaxRates\BelarusValueAddedTax;

class Belarus extends Plugin implements Jurisdiction
{
    /**
     * @var Currency
     */
    private $currency;
    /**
     * @var TaxRate
     */
    private $tax;

    /**
     * Create a new Jurisdiction
     *
     */
    public function __construct()
    {
        $this->tax = new BelarusValueAddedTax;
        $this->currency = $this->session->has('currency') ? new Currency($this->session->get('currency'))  : new Currency('BYR');
    }

    /**
     * Return the Tax Rate
     *
     * @return TaxRate
     */
    public function rate()
    {
        return $this->tax;
    }

    /**
     * Return the currency
     *
     * @return Currency
     */
    public function currency()
    {
        return $this->currency;
    }
}