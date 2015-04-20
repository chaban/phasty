<?php namespace Phasty\Common\Repo\AttributeGroups;

interface AttributeGroupsInterface {

	/**
	 * Retrieve resource by id
	 * regardless of status
	 *
	 * @param  int $id Article ID
	 * @return stdObject object of resource information
	 */
	public function byId($id);

	/**
	 * Get all resources
	 *
	 *
	 * @return StdClass Object with all groups
	 */
	public function all();

	/**
	 * Create a new Article
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data);

	/**
	 * Update an existing Group
	 *
	 * @param int id of resource
	 * @param array  Data to update an Group
	 * @return boolean
	 */
	public function update($id, array $data);

	/**
	 * Delete an existing Group
	 *
	 * @param int id of resource
	 * @return boolean
	 */
	public function delete($id);

}
