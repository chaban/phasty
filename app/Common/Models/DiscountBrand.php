<?php namespace Phasty\Common\Models;

use Phalcon\Mvc\Model;

class DiscountBrand extends Model
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
    public $brandId;

    public function getSource()
    {
        return 'discount_brand';
    }


}
