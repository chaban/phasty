<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phasty\Common\Service\Behaviors\SlugBehavior;

class Pages extends Model
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $slug;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var integer
     */
    public $active;

    /**
     *
     * @var string
     */
    public $createdAt;

    /**
     *
     * @var string
     */
    public $updatedAt;

    /**
     *
     * @var string
     */
    public $seoDescription;

    /**
     *
     * @var string
     */
    public $seoKeywords;

    /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->addBehavior(new Timestampable(
            ['beforeCreate' => ['field' => 'createdAt', 'format' => 'Y-m-d H:i:s'],
                'beforeUpdate' => ['field' => 'updatedAt', 'format' => 'Y-m-d H:i:s']
            ]));
        $this->addBehavior(new SlugBehavior([
            'slug_col' => 'slug', //The column name for the slug
            'title_col' => 'name', //The column name for the unqiue url
            'pk_col' => 'id', //Primary key
            'overwrite' => true, //Overwrite slug when updating
            'url_decode' => false //Decode url only usefull if you want to support high unicode characters in url
        ]));
    }

    public function beforeValidationOnCreate()
    {
        if (!$this->active) { // use default value if the value is not set
            $this->active = new RawValue('default');
        }
    }

    public function beforeCreate(){
        if(!$this->updatedAt){
            $this->updatedAt = new RawValue('Now()');
        }
    }

}
