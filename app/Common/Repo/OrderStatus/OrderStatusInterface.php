<?php namespace Phasty\Common\Repo\OrderStatus;

interface OrderStatusInterface {

	/**
	 * Retrieve resource by id
	 * regardless of status
	 *
	 * @param  int $id Resource ID
	 * @return stdObject object of resource information
	 */
	public function byId($id);

	/**
	 * Retrieve all resources
	 * regardless of status
	 *
	 * @return stdObject object of resource information
	 */
	public function getAll();

	/**
	 * Create a new Resource
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data);

	/**
	 * Update an existing Resource
	 *
	 * @param int id of resource
	 * @param array  Data to update an Resource
	 * @return boolean
	 */
	public function update($id, array $data);

}
