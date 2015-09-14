<?php namespace Phasty\Common\Repo\Discount;

use Phasty\Common\Models\Brands;
use Phasty\Common\Models\Categories;
use Phasty\Common\Models\DiscountBrand;
use Phasty\Common\Models\DiscountCategory;
use Phasty\Common\Models\Discounts;

class PhalconDiscount {

	protected $discount;

	// Class expects an Eloquent model
	public function __construct() {
		$this->model = new Discounts();
	}

    /**
     * Get all resources
     *
     *
     * @return StdClass Object
     */
    public function all() {
        $discounts = $this->model->find();
        if (!$discounts) {
            return false;
        }
        $result = new \StdClass();
        $result->discounts = $discounts->toArray();
        return $result;
    }

	/**
	 * Retrieve discount by id
	 * regardless of status
	 *
	 * @param  int $id discount ID
	 * @return stdObject object of discount information
	 */
	public function byId($id) {
		$brands = Brands::find(['columns' => 'id, name']);
		$categories = Categories::find(['id > 1' ,'columns' => 'id, title']);
		$discount = $this->model->findFirst("id = '$id'");
		if (!$discount || !$brands || !$categories) {
			return false;
		}
		$result = new \StdClass();
		$c['brands'] = $brands->toArray();
		$c['categories'] = $categories->toArray();
		$brand_ids = [];
		foreach ($discount->brands as $brand) {
			$brand_ids[] = $brand->id;
		}
		$category_ids = [];
		foreach ($discount->categories as $category) {
			$category_ids[] = $category->id;
		}
		$c['brandIds'] = $brand_ids;
		$c['categoryIds'] = $category_ids;
		$result->discount = array_merge($discount->toArray(), $c);
		return $result;
	}

	/**
	 * Create a new discount
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data) {
		if (!$this->model->create($data, $this->model->getWhiteList())) {
			return false;
		}
        $discount = $this->model->findFirst(['order' => 'id desc']);
        if (count($data['brandIds'])) {
            foreach($data['brandIds'] as $brandId){
                $discountBrand = new DiscountBrand();
                $discountBrand->discountId = $discount->id;
                $discountBrand->brandId = $brandId;
                $discountBrand->save();
            }
        }
        if (count($data['categoryIds'])) {
            foreach($data['categoryIds'] as $categoryId){
                $discountCategory = new DiscountCategory();
                $discountCategory->discountId = $discount->id;
                $discountCategory->categoryId = $categoryId;
                $discountCategory->save();
            }
        }
		return true;
	}

	/**
	 * Update an existing discount
	 *
	 * @param int id of the discount
	 * @param array  Data to update an discount
	 * @return boolean
	 */
	public function update($id, array $data) {
		$discount = $this->model->findFirst("id = '$id'");
		if (!$discount) {
			return false;
		}
		if (!$discount->update($data, $this->model->getWhiteList())) {
			return false;
		}
		if (count($data['brandIds'])) {
            $discountBrands = DiscountBrand::find("discountId = '$id'");
            foreach($discountBrands as $temp){
                $temp->delete();
            }
            foreach($data['brandIds'] as $brandId){
                $discountBrand = new DiscountBrand();
                $discountBrand->discountId = $id;
                $discountBrand->brandId = $brandId;
                $discountBrand->save();
            }
		}
		if (count($data['categoryIds'])) {
            $discountCategories = DiscountCategory::find("discountId = '$id'");
            foreach($discountCategories as $temp){
                $temp->delete();
            }
            foreach($data['categoryIds'] as $categoryId){
                $discountCategory = new DiscountCategory();
                $discountCategory->discountId = $id;
                $discountCategory->categoryId = $categoryId;
                $discountCategory->save();
            }
		}
		return true;
	}

	/**
	 * Delete an existing Resource
	 *
	 * @param int id of resource
	 * @return boolean
	 */
	public function delete($id) {
		return $this->model->findFirst("id = '$id'")->delete();
	}

}
