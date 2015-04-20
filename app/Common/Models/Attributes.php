<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class Attributes extends Model
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
    public $group_id;

    /**
     * @var string
     *
     */
    public $name;

    /**
     * @var integer
     *
     */
    public $filter;

    /**
     * @var integer
     *
     */
    public $position;

    /**
     * @var string
     *
     */
    public $template;

    public function getSource(){
        return 'product_attributes';
    }

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'categoryId', 'name', 'filter', 'position', 'template'
        ];
    }


    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('categoryId', '\Phasty\Common\Models\Categories', 'id', ['alias' => 'Category']);
    }

}
