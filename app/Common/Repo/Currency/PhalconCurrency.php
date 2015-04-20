<?php namespace Phasty\Common\Repo\Currency;

use Phasty\Common\Models\Currencies;

class PhalconCurrency {

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

	/**
	 * Create a new currency
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data) {
		return $this->model->create($data, $this->model->getWhiteList());
	}

	/**
	 * Update an existing currency
	 *
	 * @param int id of the currency
	 * @param array  Data to update an currency
	 * @return boolean
	 */
	public function update($id, array $data) {
		return $this->model->findFirst("id = '$id'")->update($data, $this->model->getWhiteList());
	}

	/**
	 * Delete an existing Resource
	 *
	 * @param int id of resource
	 * @return boolean
	 */
	public function delete($id) {
		return $this->model->findFirst("id = '$id'")->delete();
	}

}
