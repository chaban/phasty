<?php namespace Phasty\Common\Repo\Attributes;

use Phasty\Common\Models\Attributes;
use Phasty\Common\Models\Categories;
use Phasty\Common\Repo\Repo;

class PhalconAttributes extends Repo
{

    protected $model;

    // Class expects an Eloquent model
    public function __construct()
    {
        $this->model = new Attributes();
    }

    /**
     * Retrieve attribute by id
     * regardless of status
     *
     * @param  int $id attribute ID
     * @return stdObject object of attribute information
     */
    public function byId($id)
    {
        $categories = Categories::find(["id > 1", 'columns' => 'id as value, title as label']);
        $attribute = $this->model->findFirst("id = '$id'");
        if (!$attribute || !$categories) {
            return false;
        }
        $result = new \StdClass();
        $c['categories'] = $categories->toArray();
        $result->attribute = array_merge($attribute->toArray(), $c);
        return $result;
    }

    /**
     * Get all resources
     *
     * @param int categoryId
     * @return StdClass Object with all attributes
     */
    public function all($categoryId = null)
    {
        if($categoryId) {
            $categoryId = $this->filter->sanitize($categoryId, 'int');
            $category = Categories::findFirst("id = '$categoryId'");
            $parents = $category->parents()->toArray();
            $categoryIds = [];
            //get all attributes from parent categories
            foreach ($parents as $parent) {
                $categoryIds[] = $parent['id'];
            }
            $categoryIds[] = $categoryId;
            $attributes = $this->model->query()->inWhere('categoryId', $categoryIds)->execute();
        }else{
            $attributes = $this->model->find();
        }
        if (!$attributes) {
            return false;
        }
        $temp = [];
        foreach ($attributes as $attribute) {
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

}
