<?php namespace Phasty\Common\Models;

use Phalcon\Db;
use Phasty\Common\Service\Behaviors\SlugBehavior;
use Phalcon\Mvc\Model;
use Phalcon\NestedSets\NestedSets;
use utilphp\util;

class Categories extends NestedSets
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var string
     *
     */
    public $slug;

    /**
     * @var string
     *
     */
    public $title;

    /**
     * @var integer
     *
     */
    public $lft;

    /**
     * @var integer
     *
     */
    public $rgt;

    /**
     * @var integer
     *
     */
    //public $level;

    /**
     * @var integer
     */
    //public $parent_id;

    /**
     * @var integer
     */
    public $depth;


    public function getSource()
    {
        return 'categories';
    }


    /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->useDynamicUpdate(true);

        $this->addBehavior(new SlugBehavior([
            'slug_col' => 'slug', //The column name for the slug
            'title_col' => 'title', //The column name for the unqiue url
            'pk_col' => 'id', //Primary key
            'overwrite' => true, //Overwrite slug when updating
            'url_decode' => false //Decode url only usefull if you want to support high unicode characters in url
        ]));

        $this->hasMany('id', '\Phasty\Common\Models\Attributes', 'categoryId', ['alias' => 'Attributes',
            "foreignKey" => [
                "message" => "The category cannot be deleted because some attribute groups are using it"
            ]]);

        $this->hasMany('id', '\Phasty\Common\Models\Products', 'categoryId', ['alias' => 'Products',
            "foreignKey" => [
                "message" => "The category cannot be deleted because some products are using it"
            ]]);

        $this->hasManyToMany(
            'id',
            '\Phasty\Common\Models\DiscountCategory',
            'categoryId', 'discountId',
            '\Phasty\Common\Models\Discounts',
            'id',
            ['alias' => 'Discount'/*),
                "foreignKey" => [
                    "message" => "The category cannot be deleted because some products are using it"]*/]
        );
    }

    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param  array $attributes
     * @return static
     */
    public static function firstOrNew(array $attributes)
    {
        $id = $attributes['id'];
        if (($instance = static::findFirst("id = '$id'"))) {
            return $instance;
        }

        $new = new static;
        $new->id = $id;
        return $new;
    }

    public function getAttributesForSearchBlock(){
        $parents = $this->parents()->toArray();
        unset($parents[0]);
        $attributes = [];
        $productIds = '';
        if(is_array($parents) && isset($parents[1])) {
            foreach ($parents as $parent) {
                if(($products = Products::find(["categoryId = :id:", 'bind' => ['id' => $parent['id']], 'columns' => 'id']))){
                    foreach($products as $product){
                        $productIds .= $product->id . ',';
                    }
                }
                if(($attribute = Attributes::find(["categoryId = :id: and filter = 'Y' order by position asc", 'bind' => ['id' => $parent['id']]]))){
                    $attributes[] = $attribute->toArray();
                }
            }
        }
        foreach($this->products->toArray() as $product){
            $productIds .= $product['id'] . ',';
        }
        $attributes[] = $this->attributes->toArray();
        $attributes = call_user_func_array('array_merge', $attributes);
        $attributeValues  = $this->getAttributeValuesForSearchBlock($attributes, trim($productIds, ','));
        return [$attributes, $attributeValues];
    }

    protected function getAttributeValuesForSearchBlock($attributes = [], $productIds = ''){
        $db = $this->getDI()->getDb();
        $attributeValues = [];
        foreach($attributes as $attribute){
            $name = $attribute['name'];
            $sql = "SELECT COLUMN_GET(attributeValues, '$name' as CHAR) as value FROM product_attribute_values WHERE productId IN ($productIds)";
            foreach($db->fetchAll($sql, Db::FETCH_ASSOC) as $row){
                if($row['value'])
                $attributeValues[$name][$row['value']] = $row['value'];
            }
        }
        return $attributeValues;
    }
}
