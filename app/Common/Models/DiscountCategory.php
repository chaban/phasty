<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class DiscountCategory extends Model
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
    public $discountId;

    /**
     * @var integer
     *
     */
    public $categoryId;

    public function getSource()
    {
        return 'discount_category';
    }


}
