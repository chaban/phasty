<?php namespace Phasty\Common\Repo\Categories;

interface CategoriesInterface {

	/**
	 * Retrieve all categories
	 * regardless of status
	 *
	 * @return array of all categories
	 */
	public function all();

	/**
	 * Retrieve Category by id
	 * regardless of status
	 *
	 * @param  int $id Category ID
	 * @return stdObject object of Category information
	 */
	public function byId($id);

	/**
	 * Get single Category by URL
	 *
	 * @param string  URL slug of Category
	 * @return object object of Category information
	 */
	public function bySlug($slug);

	/**
	 * Create a new Category
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data);

	/**
	 * Update an existing Category
	 * @param integer id
	 * @param array  Data to update an Category
	 * @return boolean
	 */
	public function update($id, array $data);

}
