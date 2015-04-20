<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class AttributeGroups extends Model
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
    public $category_id;

    /**
     * @var string
     *
     */
    public $name;

    /**
     * @var integer
     *
     */
    public $position;

    public function getSource(){
        return 'product_attribute_groups';
    }


    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->hasMany('id', '\Phasty\Common\Models\Attributes', 'group_id', ['alias' => 'Attributes',
            "foreignKey" => [
                "message" => "The attribute group cannot be deleted because some attributes are using it"
            ]
        ]);
        $this->belongsTo('category_id', '\Phasty\Common\Models\Categories', 'id', ['alias' => 'Category']);
    }

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'category_id', 'name', 'position'
        ];
    }

}
