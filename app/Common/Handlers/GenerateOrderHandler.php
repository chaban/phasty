<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Repo\Currency\CurrencyCache;
use PhilipBrown\Basket\Converter;
use PhilipBrown\Basket\Discounts\PercentageDiscount;
use PhilipBrown\Basket\Basket;
use Phasty\Common\Service\Basket\Jurisdictions\Belarus;
use PhilipBrown\Basket\MetaData\TotalMetaData;
use PhilipBrown\Basket\MetaData\ProductsMetaData;
use PhilipBrown\Basket\MetaData\TaxMetaData;
use PhilipBrown\Basket\MetaData\ValueMetaData;
use PhilipBrown\Basket\MetaData\TaxableMetaData;
use PhilipBrown\Basket\MetaData\DeliveryMetaData;
use PhilipBrown\Basket\MetaData\DiscountMetaData;
use PhilipBrown\Basket\MetaData\SubtotalMetaData;
use PhilipBrown\Basket\Processor;
use PhilipBrown\Basket\Reconcilers\DefaultReconciler;
use Money\Currency;
use Money\Money;
use PhilipBrown\Basket\Transformers\ArrayTransformer;

class GenerateOrderHandler extends Plugin implements CommandHandler
{

    private $currencies;
    private $currenciesCache;

    public function __construct()
    {
        $this->currenciesCache = new CurrencyCache();
        $this->currencies = $this->currenciesCache->all();
    }

    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        // how basket works look here - https://github.com/philipbrown/basket
        $basket = new Basket(new Belarus());
        $currencyInBasket = $basket->currency()->getName();
        foreach ($this->cart->contents() as $item) {
            $price = null;
            $sku = $item->sku;
            $name = $item->name;
            $currencyInCart = $item->currency;
            if ($currencyInBasket !== $currencyInCart) {
                $price = $this->currenciesCache->convertPrice($item->price, $currencyInCart, $currencyInBasket);
                $price = new Money((int)$price, new Currency($currencyInBasket));
            } else {
                $price = new Money((int)$item->price, new Currency($currencyInBasket));
            }

            $basket->add($sku, $name, $price, function ($product) use ($item) {
                $product->quantity((int)$item->quantity);
                if (!empty($item->options['discount']))
                    $product->discount(new PercentageDiscount((int)$item->options['discount']['sum']));
            });
        }
        //$this->utils->var_dump($basket);die;
        $reconciler = new DefaultReconciler;

        $meta = [
            new DeliveryMetaData($reconciler),
            new DiscountMetaData($reconciler),
            new ProductsMetaData,
            new SubtotalMetaData($reconciler),
            new TaxableMetaData,
            new TaxMetaData($reconciler),
            new TotalMetaData($reconciler),
            new ValueMetaData($reconciler)
        ];

        $processor = new Processor($reconciler, $meta);

        $transformer = new ArrayTransformer(new Converter());
        $order = $processor->process($basket);
        return $transformer->transform($order);
    }
}