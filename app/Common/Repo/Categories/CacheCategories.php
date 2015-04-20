<?php namespace Phasty\Common\Repo\Categories;

use Phalcon\Mvc\User\Plugin;

class CacheCategories extends Plugin{
    protected $repo;
    public function __construct(){
        $this->repo = new PhalconCategories();
    }

    public function all(){
        $key = md5('allCategories');
        if( $this->modelsCache->exists($key) )
        {
            return $this->modelsCache->get($key);
        }
        $categories = $this->repo->all();
        $this->modelsCache->save($key, $categories);
        return $categories;
    }
}