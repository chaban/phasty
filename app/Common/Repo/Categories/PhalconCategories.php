<?php namespace Phasty\Common\Repo\Categories;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Categories;
use utilphp\util;

class PhalconCategories extends Plugin
{

    protected $model;

    // Class expects an Phalcon model
    public function __construct()
    {
        $this->model = new Categories();
    }

    /**
     * Retrieve resource flat array
     *
     * @return stdObject object of resource information
     */
    public function allAsFlatArray()
    {
        $categories = $this->model->find(['order'=>'id', 'columns' => 'id, title']);
        if (!$categories) {
            return false;
        }
        $result = new \StdClass();
        $result->categories = $categories->toArray();
        return $result;
    }

    /**
     * Retrieve resource tree
     *
     * @return stdObject object of resource information
     */
    public function all()
    {
        $tree = $this->getChildren($this->model->roots());
        $result = new \StdClass();
        $result->categories = $tree;
        return $result;
    }

    /**
     * Retrieve resource by id
     * regardless of status
     *
     * @param  int $id resource ID
     * @return stdObject object of resource information
     */
    public function byId($id)
    {
        $resource = $this->model->findFirst(["id = '$id'",'columns' => 'id, title']);
        if (!$resource) {
            return false;
        }
        $result = new \StdClass();
        $result->category = $resource->toArray();
        return $result;
    }

    /**
     * Get single resource by URL
     *
     * @param string  URL slug of resource
     * @return object object of resource information
     */
    public function bySlug($slug)
    {
        return $this->model->with('status')
            ->with('author')
            ->with('tags')
            ->where('slug', $slug)
            ->where('status_id', 1)
            ->first();
    }

    /**
     * Update an existing resource
     *
     * @param int id of the resource
     * @param array|mixed  Data to update an resource
     * @param string update method
     * @return boolean
     */
    public function update($id, $data)
    {
        if(!isset($data->children)) {
            return $this->model->findFirst("id = '$id'")->update(['title' => $data->title]);
        }else{
            $parent = $this->model->findFirst("id = '$id'");
            $child = new Categories();
            return $child->prependTo($parent, ['title' => $data->children->title]);
        }
    }

    /**
     * Delete an existing resource
     *
     * @param int id of the resource
     * @return boolean
     */
    public function delete($id)
    {
        return $this->model->findFirst("id = '$id'")->deleteWithChildren();
        //return $node->delete();
    }

    /*protected function buildTree() {
        $root = $this->model->roots();
        $tree = [];

        foreach($roots as $root){
            util::var_dump($root);die;
            $id = $root->id;
            $tree[$id] = $root->toArray();
            if (!$root->isLeaf())
                $tree[$id]['children'] = $this->getChildren($root, $tree);
        }
        $id = $root->id;
        $tree[$id] = $root->toArray();
        $tree[$id]['children'] = $this->getChildren($root, $tree);

        return $tree;
    }*/

    protected function getChildren($element){
        $tree = array();
        $roots = $element->children(1);
        foreach ($roots as $root) {
            $id = $root->id;
            $tree[$id]['id'] = $id;
            $tree[$id]['title'] = $root->title;
            $tree[$id]['slug'] = $root->slug;
            if (!$root->isLeaf()) {
                $tree[$id]['children'] = $this->getChildren($root, $tree);
            }
        }
        return array_values($tree);
    }

}
