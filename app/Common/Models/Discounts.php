<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class Discounts extends Model
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
    public $name;

    /**
     * @var integer
     *
     */
    public $active;

    /**
     * @var string
     *
     */
    public $sum;

    /**
     * @var string
     *
     */
    public $startDate;

    /**
     * @var string
     *
     */
    public $endDate;

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'name', 'active', 'sum', 'startDate', 'endDate'
        ];
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->hasManyToMany(
            'id',
            '\Phasty\Common\Models\DiscountBrand',
            'discountId', 'brandId',
            '\Phasty\Common\Models\Brands',
            'id',
            ['alias' => 'Brands'/*),
                "foreignKey" => [
                    "message" => "The category cannot be deleted because some products are using it"]*/]
        );
        $this->hasManyToMany(
            'id',
            '\Phasty\Common\Models\DiscountCategory',
            'discountId', 'categoryId',
            '\Phasty\Common\Models\Categories',
            'id',
            ['alias' => 'Categories'/*,
                "foreignKey" => [
                    "message" => "The category cannot be deleted because some products are using it"]*/]
        );
    }


}
