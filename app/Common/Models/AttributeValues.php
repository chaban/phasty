<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class AttributeValues extends Model
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var integer
     *
     */
    public $productId;

    /**
     * @var string
     *
     */
    public $attributeValues;

    /**
     * @var integer
     *
     */
    public $position;

    public function getSource(){
        return 'product_attribute_values';
    }


    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('productId', '\Phasty\Common\Models\Products', 'id', array('alias' => 'Product'));
    }

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'productId', 'attributeValues'
        ];
    }

}
