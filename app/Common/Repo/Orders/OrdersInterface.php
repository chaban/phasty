<?php namespace Phasty\Common\Repo\Orders;

interface OrdersInterface {

	/**
	 * Retrieve resource by id
	 * regardless of status
	 *
	 * @param  int $id Resource ID
	 * @return stdObject object of resource information
	 */
	public function byId($id);

	/**
	 * Get paginated resources
	 *
	 * @param array $params array from _GET[]
	 *
	 * @return StdClass Object with $items and $totalItems for pagination
	 */
	public function byPage($params);

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
