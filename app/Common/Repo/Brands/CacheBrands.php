<?php namespace Phasty\Common\Repo\Brands;

use Phalcon\Mvc\User\Plugin;

class CacheBrands extends Plugin{
    protected $repo;
    public function __construct(){
        $this->repo = new PhalconBrands();
    }

    public function all(){
        $key = md5('allBrands');
        if( $this->modelsCache->exists($key) )
        {
            return $this->modelsCache->get($key);
        }
        $models = $this->repo->all();
        $this->modelsCache->save($key, $models);
        return $models;
    }
}