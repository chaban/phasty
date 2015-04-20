<?php namespace Phasty\Common\Service\Widgets;

use Phalcon\Widgets\AbstractWidget;
use Phasty\Common\Repo\Currency\CurrencyCache;

class ChooseCurrency extends AbstractWidget {
    protected $currency; //current currency, initialized by parent abstract class
    protected $currencies;// all currencies
    protected $view; // simple views service for widgets

    /**
     * initialize variables
     */
    public function initialize(){
        $this->view = (new ViewInjector())->inject();
        $this->currencies = (new CurrencyCache())->all();
    }

    public function run()
    {
        echo $this->view->render('chooseCurrency', ['currency' => $this->currency, 'currencies' => $this->currencies]);
    }
}