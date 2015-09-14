<?php namespace Phasty\Common\Repo\Currency;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Currencies;

class CurrencyCache extends Plugin{

    protected $model;

    // Class expects an Eloquent model
    public function __construct() {
        $this->model = new Currencies();
    }

    /**
     * Get all resources
     *
     *
     * @return StdClass Object
     */
    public function all() {
        $key = md5('all-currencies-as-array');
        if( $this->cache->exists($key) )
        {
            return $this->cache->get($key);
        }
        $rows = $this->model->find();
        $currencies = [];
        foreach ($rows as $result) {
            $currencies[$result->name] = $result->toArray();
        }
        $this->cache->save($key, $currencies);
        return $currencies;
    }

    /**
     * convert currency from one currency to anothe
     * @param $value
     * @param $from
     * @param $to
     * @return float
     */
    public function convertPrice($value, $from, $to)
    {
        $currencies = $this->all();
        if (isset($currencies[$from])) {
            $from = floatval($currencies[$from]['rate']);
        } else {
            $from = 0;
        }

        if (isset($currencies[$to])) {
            $to = floatval($currencies[$to]['rate']);
        } else {
            $to = 0;
        }
        return (floatval($value) * $to) / $from;
    }
}
