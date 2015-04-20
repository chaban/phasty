<?php namespace Phasty\Common\Repo\AttributeGroups;

use Phalcon\Mvc\Model;
use Phasty\Common\Models\AttributeGroups;
use Phasty\Common\Models\Categories;

class PhalconAttributeGroups {

	protected $model;

	// Class expects an Eloquent model
	public function __construct() {
		$this->model = new AttributeGroups();
	}

	/**
	 * Retrieve group by id
	 * regardless of status
	 *
	 * @param  int $id group ID
	 * @return stdObject object of group information
	 */
	public function byId($id) {
		$categories = Categories::find(['columns' => 'id, title']);
		$group = $this->model->findFirst("id = '$id'");
        if (!$group || !$categories) {
            return false;
        }
		$result = new \StdClass();
		$c['categories'] =  $categories->toArray();
		$result->attributeGroup = array_merge($group->toArray(), $c);
		return $result;
	}

	/**
	 * Get all resources
	 *
	 *
	 * @return StdClass Object with all groups
	 */
	public function all() {
		$groups = $this->model->find(['columns' => 'id, name, position']);
		if (!$groups) {
			return false;
		}
		$result = new \StdClass();
		$result->attributeGroups = $groups->toArray();
		return $result;
	}

	/**
	 * Create a new group
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data) {
		//$group = new AttributeGroups();
        return $this->model->save($data, $this->model->getWhiteList());
	}

	/**
	 * Update an existing group
	 *
	 * @param int id of the group
	 * @param array  Data to update an group
	 * @return boolean
	 */
	public function update($id, array $data) {
		return $this->model->findFirst("id = '$id'")->update($data, $this->model->getWhiteList());
	}

	/**
	 * Delete an existing Group
	 *
	 * @param int id of resource
	 * @return boolean
	 */
	public function delete($id) {
		return $this->model->findFirst("id = '$id'")->delete();
	}

    /**
     * @param  $categories
     * @return array
     */
    protected function getLeafs($categories){
        $temp = [];
        foreach($categories as $category){
            if($category->isLeaf())
            $temp[$category->id] = $category->title;
        }
        return $temp;
    }

}
