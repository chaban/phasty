<?php namespace Phasty\Common\Models;


use Phalcon\Mvc\Model;

class Brands extends Model
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
	 * @var string
	 *
	 */
	public $description;
  
  /**
	 * @var string
	 *
	 */
	public $seoTitle;
  
  /**
	 * @var string
	 *
	 */
	public $seoKeywords;
  
  /**
	 * @var string
	 *
	 */
	public $seoDescription;

    public function initialize(){
        $this->useDynamicUpdate(true);
        $this->hasManyToMany(
            'id',
            '\Phasty\Common\Models\DiscountBrand',
            'brandId', 'discountId',
            '\Phasty\Common\Models\Discounts',
            'id',
            ['alias' => 'Discount'/*),
                "foreignKey" => [
                    "message" => "The category cannot be deleted because some products are using it"]*/]
        );
    }

    /**
     * @return array
     */
    public static function getWhiteList()
    {
        return [
            'name', 'description', 'seoTitle', 'seoKeywords', 'seoDescription'
        ];
    }


}
