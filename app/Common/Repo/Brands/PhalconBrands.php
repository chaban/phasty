<?php namespace Phasty\Common\Repo\Brands;


use Phasty\Common\Models\Brands;
use Phasty\Common\Repo\Repo;

class PhalconBrands extends Repo {

	protected $model;

	// Class expects an Eloquent model
	public function __construct() {
		$this->model = new Brands();
	}

	/**
	 * Retrieve brand by id
	 * regardless of status
	 *
	 * @param  int $id brand ID
	 * @return stdObject object of brand information
	 */
	public function byId($id) {
		$brand = $this->model->findFirst("id = '$id'");
		if (!$brand) {
			return false;
		}
		$result = new \StdClass();
		$result->brand = $brand->toArray();
		return $result;
	}

	/**
	 * Get all resources
	 *
	 *
	 * @return StdClass Object
	 */
	public function all() {
		$brands = $this->model->find(['columns' => 'id, name']);
		if (!$brands) {
			return false;
		}
		$result = new \StdClass();
		$result->brands = $brands->toArray();
		return $result;
	}
}
