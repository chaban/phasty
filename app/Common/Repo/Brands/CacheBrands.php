<?php namespace Phasty\Common\Repo\Brands;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Products;
use Phasty\Common\Models\Brands;

class CacheBrands extends Plugin{
    protected $repo;
    public function __construct(){
        $this->repo = new PhalconBrands();
    }

    public function all(){
        $key = md5('allBrands');
        if( $this->cache->exists($key) )
        {
            return $this->cache->get($key);
        }
        $models = $this->repo->all();
        $this->cache->save($key, $models);
        return $models;
    }

    public function getBrandsForCategory($categoryId = null)
    {
        if ($categoryId) {
            $products = Products::find(['conditions' => "categoryId = '$categoryId'", 'columns' => 'brandId']);
            if (!$products) {
                return false;
            }
            $brandIds = [];
            foreach($products as $product){
                $brandIds[] = $product['brandId'];
            }
            $key = 'brands_for_category_'.$categoryId;
            if($this->cache->exists($key) )
            {
                return $this->cache->get($key);
            }
            $brands = Brands::query()->inWhere('id', $brandIds)->columns('id, name')->execute()->toArray();
            $this->cache->save($key, $brands);
            return $brands;
        }
        return false;
    }
}