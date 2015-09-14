<?php namespace Phasty\Common\Models;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model;
use Phasty\Common\Service\Behaviors\SlugBehavior;
use Phasty\Common\Service\Helpers\CFileHelper;
use utilphp\util;

class Products extends Model
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
    public $categoryId;

    /**
     * @var integer
     *
     */
    public $brandId;

    /**
     * @var string
     *
     */
    public $name;

    /**
     * @var string
     *
     */
    public $slug;

    /**
     * @var double
     *
     */
    public $price;

    /**
     * @var double
     *
     */
    public $maxPrice;

    /**
     * @var integer
     *
     */
    public $active;

    /**
     * @var string
     *
     */
    public $sku;

    /**
     * @var integer
     *
     */
    public $quantity;

    /**
     * @var integer
     *
     */
    public $availability;

    /**
     * @var integer
     *
     */
    public $autoDecreaseQuantity;

    /**
     * @var integer
     *
     */
    public $viewsCount;

    /**
     * @var string
     *
     */
    public $createdAt;

    /**
     * @var string
     *
     */
    public $updatedAt;

    /**
     * @var integer
     *
     */
    public $addedToCartCount;


    /**
     * @var integer
     *
     */
    public $rating;

    /**
     * @var string
     *
     */
    public $shortDescription;

    /**
     * @var string
     *
     */
    public $fullDescription;

    /**
     * @var string
     *
     */
    public $attributeValues;

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'categoryId', 'brandId', 'name', 'price', 'maxPrice', 'active', 'quantity',
            'availability', 'autoDecreaseQuantity', 'shortDescription', 'fullDescription'
        ];
    }

    /**
     * Initializer method for model.
     */
    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->addBehavior(new SlugBehavior([
            'slug_col' => 'slug', //The column name for the slug
            'title_col' => 'name', //The column name for the unqiue url
            'pk_col' => 'id', //Primary key
            'overwrite' => false, //Overwrite slug when updating
            'url_decode' => false //Decode url only usefull if you want to support high unicode characters in url
        ]));
        $this->belongsTo('brandId', '\Phasty\Common\Models\Brands', 'id', array('alias' => 'Brand'));
        $this->belongsTo('categoryId', '\Phasty\Common\Models\Categories', 'id', array('alias' => 'Category'));
        $this->hasMany('id', '\Phasty\Common\Models\Reviews', 'productId', array('alias' => 'Reviews'));
        $this->hasOne('id', '\Phasty\Common\Models\AttributeValues', 'productId', array('alias' => 'AttributeValue'));
    }

    public function beforeValidationOnCreate()
    {
        if (!$this->createdAt)
            $this->createdAt = new RawValue('NOW()');
        if (!$this->updatedAt)
            $this->updatedAt = new RawValue('NOW()');
        if (!$this->sku)
            $this->sku = md5($this->name);
        if (!$this->shortDescription)
            $this->shortDescription = '';
        if (!$this->attributeValues)
            $this->attributeValues = '';
        $this->viewsCount = 0;
        $this->addedToCartCount = 0;
        $this->rating = 0;
    }

    public function beforeValidationOnUpdate()
    {
        if (!$this->createdAt) {
            $this->createdAt = new RawValue('NOW()');
        }

        $this->updatedAt = new RawValue('NOW()');
    }

    public function afterCreate()
    {
        $attributes = $this->category->attributes->toArray();
        $sql = "INSERT INTO product_attribute_values VALUES('null', '$this->id', COLUMN_CREATE(";
        $params = [];
        $last_key = count($attributes);
        foreach ($attributes as $key => $value) {
            if ($key < $last_key - 1) {
                $sql .= " ?, ?,";
            } else {
                $sql .= " ?, ?";
            }
            $params[] = $value['name'];
            $params[] = '';
        }
        $sql .= "))";
        $this->getDI()->getDb()->execute($sql, $params);
    }

    public function afterDelete()
    {
        $this->attributeValue->delete();
        $config = $this->getDI()->getConfig();
        $imagesPath = BASE_DIR . DIRECTORY_SEPARATOR . 'public' . $config->app->productImagesPath . $this->id;
        util::rmdir($imagesPath);
    }

    public function saveCounter($name = '')
    {
        if ($name != '') {
            if ($name == 'view') {
                $views_count = $this->viewsCount;
                $views_count = $views_count + 1;
                $this->viewsCount = $views_count;
                $this->update();
                $this->refresh();
            } elseif ($name == 'cart') {
                $added_to_cart_count = $this->addedToCartCount;
                $added_to_cart_count = $added_to_cart_count + 1;
                $this->addedToCartCount = $added_to_cart_count;
                $this->update();
                $this->refresh();
            }
        }
    }

    public function decreaseQuantity()
    {
        if ($this->autoDecreaseQuantity && (int)$this->quantity > 0) {
            $quantity = $this->quantity;
            $quantity = $quantity - 1;
            $this->quantity = $quantity;
            $this->update();
            $this->refresh();
        }
    }

    public function getImages($all = false)
    {
        $config = $this->getDI()->getConfig();
        $imagesFolder = BASE_DIR . DIRECTORY_SEPARATOR . 'public' . $config->app->productImagesPath . $this->id;
        $images = null;
        if (is_dir($imagesFolder)) {
            $images = CFileHelper::findFiles($imagesFolder, ['fileTypes' => ['jpg', 'jpeg', 'gif', 'png'], 'level' => 0,]);
        }

        if ($all && is_array($images)) {
            return $images;
        } elseif (is_array($images)) {
            return $images['0'];
        }
        return '';
    }

    public static function getMaxPrice($categoryId = null)
    {
        if ($categoryId)
            $maxPrice = self::findFirst(["categoryId = '$categoryId'", 'order' => 'price DESC',
                "cache" => ["key" => "maxPriceWithCategory_" . $categoryId]]);
        else
            $maxPrice = self::findFirst(['order' => 'price desc', "cache" => ["key" => "productsMaxPrice"]]);
        if ($maxPrice) {
            return $maxPrice->price;
        }
        return false;
    }

}
