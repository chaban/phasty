<?php namespace Phasty\Common\Service\Widgets;

use Money\Currency;
use Money\Money;
use Phalcon\Widgets\AbstractWidget;
use Phasty\Common\Repo\Currency\CurrencyCache;
use PhilipBrown\Basket\Converter;

class Price extends AbstractWidget {
    protected $currencyCache;
    protected $value;// value from database as price of product
    protected $defaultCurrency;//currency that in configuration as default
    protected $currentCurrency;// currency that now in session
    protected $price;// value that will be displayed as price for product
    protected $converter;

    /**
     * initialize variables
     */
    public function initialize(){
        $this->converter = new Converter();
        $this->currencyCache = new CurrencyCache();
        if($this->defaultCurrency !== $this->currentCurrency){
            $this->price = $this->currencyCache->convertPrice($this->value, $this->defaultCurrency, $this->currentCurrency);
        }else{
            $this->price = $this->value;
        }
    }

    public function run()
    {
        echo $this->converter->convert(new Money((int)$this->price, new Currency($this->currentCurrency)));
    }
}