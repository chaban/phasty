<?php namespace Phasty\Common\Repo\Categories;

use Phalcon\Mvc\User\Plugin;

class CacheCategories extends Plugin{
    protected $repo;
    public function __construct(){
        $this->repo = new PhalconCategories();
    }

    public function all(){
        $key = md5('allCategories');
        if( $this->cache->exists($key))
        {
            return $this->cache->get($key);
        }
        $categories = $this->repo->all();
        $this->cache->save($key, $categories);
        return $categories;
    }
}