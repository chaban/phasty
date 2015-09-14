<?php namespace Phasty\Common\Repo\Pages;

use Phasty\Common\Repo\Repo;
use Phasty\Common\Repo\RepoAbstract;
use Phasty\Common\Models\Pages;

class PhalconPages extends Repo
{

    protected $model;

    // Class expects an Phalcon model
    public function __construct()
    {
        $this->model = new Pages();
    }

    /**
     * Retrieve page by id
     * regardless of status
     *
     * @param  int $id page ID
     * @return stdObject object of page information
     */
    public function byId($id)
    {
        $page = $this->model->findFirst(["id = '$id'",'columns' => 'id, name, content, active, seoDescription, seoKeywords']);
        if (!$page) {
            return false;
        }
        $result = new \StdClass();
        $result->page = $page->toArray();
        return $result;
    }

    /**
     * Get all resources
     *
     *
     * @return StdClass Object
     */
    public function all() {
        $pages = $this->model->find(['columns' => 'id, name, createdAt, updatedAt, active']);
        if (!$pages) {
            return false;
        }
        $result = new \StdClass();
        $result->pages = $pages->toArray();
        return $result;
    }

    /**
     * Get single page by URL
     *
     * @param string  URL slug of page
     * @return object object of page information
     */
    public function bySlug($slug)
    {
        
    }
}
