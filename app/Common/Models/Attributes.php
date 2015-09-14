<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;
use Phalcon\Db\RawValue;

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

    /**
     * @var string
     *
     */
    public $type;

    const TYPE_STRING = 1;
    const TYPE_INT = 2;
    const TYPE_BOOL = 3;

    public function getSource(){
        return 'product_attributes';
    }

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'categoryId', 'name', 'filter', 'position', 'template', 'type'
        ];
    }


    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->belongsTo('categoryId', '\Phasty\Common\Models\Categories', 'id', ['alias' => 'Category']);
    }

    public function beforeValidationOnCreate(){
        if(!$this->template){
            $this->template =  new RawValue('default');
        }
        if(!$this->type){
            $this->type =  new RawValue('default');
        }
    }

    public function beforeValidationOnUpdate(){
        if(!$this->template){
            $this->template =  new RawValue('default');
        }
        if(!$this->type){
            $this->type =  new RawValue('default');
        }
    }

}
