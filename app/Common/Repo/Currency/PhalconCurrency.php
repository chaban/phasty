<?php namespace Phasty\Common\Repo\Currency;

use Phasty\Common\Models\Currencies;
use Phasty\Common\Repo\Repo;

class PhalconCurrency extends Repo {

	protected $model;

	// Class expects an Eloquent model
	public function __construct() {
		$this->model = new Currencies();
	}

	/**
	 * Retrieve currency by id
	 * regardless of status
	 *
	 * @param  int $id currency ID
	 * @return stdObject object of currency information
	 */
	public function byId($id) {
		$currency = $this->model->findFirst("id = '$id'");
		if (!$currency) {
			return false;
		}
		$result = new \StdClass();
		$result->currency = $currency->toArray();
		return $result;
	}

	/**
	 * Get all resources
	 *
	 *
	 * @return StdClass Object
	 */
	public function all() {
		$currencies = $this->model->find();
		if (!$currencies) {
			return false;
		}
		$result = new \StdClass();
		$result->currencies = $currencies->toArray();
		return $result;
	}
}
