<?php namespace Phasty\Common\Repo\Attributes;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Attributes;
use Phasty\Common\Models\Categories;

class PhalconAttributes extends Plugin{

	protected $model;

	// Class expects an Eloquent model
	public function __construct() {
		$this->model = new Attributes();
	}

	/**
	 * Retrieve attribute by id
	 * regardless of status
	 *
	 * @param  int $id attribute ID
	 * @return stdObject object of attribute information
	 */
	public function byId($id) {
		$groups = Categories::find(["id > 1",'columns' => 'id, title']);
		$attribute = $this->model->findFirst("id = '$id'");
		if (!$attribute || !$groups) {
			return false;
		}
		$result = new \StdClass();
		$c['categories'] = $groups->toArray();
		$result->attribute = array_merge($attribute->toArray(), $c);
		return $result;
	}

	/**
	 * Get all resources
	 *
	 *
	 * @return StdClass Object with all attributes
	 */
	public function all() {
		$attributes = $this->model->find();
		if (!$attributes) {
			return false;
		}
        $temp = [];
        foreach($attributes as $attribute){
            $temp[$attribute->id]['id'] = $attribute->id;
            $temp[$attribute->id]['name'] = $attribute->name;
            $temp[$attribute->id]['filter'] = $attribute->filter;
            $temp[$attribute->id]['position'] = $attribute->position;
            $temp[$attribute->id]['category'] = $attribute->category->title;
        }
		$result = new \StdClass();
		$result->attributes = array_values($temp);
		return $result;
	}

	/**
	 * Create a new attribute
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data) {
        return $this->model->save($data, $this->model->getWhiteList());
	}

	/**
	 * Update an existing attribute
	 *
	 * @param int id of the attribute
	 * @param array  Data to update an attribute
	 * @return boolean
	 */
	public function update($id, array $data) {
		return $this->model->findFirst("id = '$id'")->update($data, $this->model->getWhiteList());
	}

	/**
	 * Delete an existing resource
	 *
	 * @param int id of resource
	 * @return boolean
	 */
	public function delete($id) {
		return $this->model->findFirst("id = '$id'")->delete();
	}

}
