<?php namespace Phasty\Common\Repo\Brands;


use Phasty\Common\Models\Brands;

class PhalconBrands {

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

	/**
	 * Create a new brand
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data) {
		return $this->model->create($data, $this->model->getWhiteList());
	}

	/**
	 * Update an existing brand
	 *
	 * @param int id of the brand
	 * @param array  Data to update an brand
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
