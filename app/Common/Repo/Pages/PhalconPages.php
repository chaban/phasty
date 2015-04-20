<?php namespace Phasty\Common\Repo\Pages;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Repo\RepoAbstract;
use Phasty\Common\Models\Pages;

class PhalconPages extends Plugin implements PagesInterface
{

    protected $pages;

    // Class expects an Phalcon model
    public function __construct()
    {
        $this->pages = new Pages();
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
        $page = $this->pages->findFirst(["id = '$id'",'columns' => 'id, name, content, active, seoDescription, seoKeywords']);
        if (!$page) {
            return false;
        }
        $result = new \StdClass();
        $result->page = $page->toArray();
        return $result;
    }

    /**
     * Get paginated pages
     * @param array $params from _GET[]
     *
     * @return StdClass Object with $items and $totalItems for pagination
     */
    public function byPage($params = array())
    {
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $pageNumber = isset($params['page']) ? $params['page'] : 0;
        $orderBy = isset($params['orderBy']) ? $params['orderBy'] : 'createdAt';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $filters = isset($params['filterByFields']) ? json_decode($params['filterByFields'], true) : null;

        $result = new \StdClass;
        $result->meta = new \StdClass;
        $result->meta->pageNumber = (int)$pageNumber;
        $result->meta->limit = (int)$limit;
        $result->meta->totalItems = 0;
        $result->pages = array();

        $builder = $this->modelsManager->createBuilder()->from('Phasty\Common\Models\Pages');
        $builder->orderBy("$orderBy  $order");

        if (is_array($filters)) {
            reset($filters);
            $first = key($filters);
            foreach ($filters as $key => $filter) {
                if ($key === $first)
                    $builder->where("$key like :filter:", ['filter' => '%' . $filter . '%']);
                $builder->orWhere("$key like :filter:", ['filter' => '%' . $filter . '%']);
            }
            $result->meta->totalItems = $builder->getQuery()->execute()->count();
        } else {
            $result->meta->totalItems = $this->pages->count();
        }

        $pages = $builder->offset($limit * ($pageNumber))
            ->columns(['id, name, active, createdAt, updatedAt'])
            ->limit($limit)
            ->getQuery()
            ->execute();

        if (!$pages) {
            return false;
        }

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
        return $this->pages->with('status')
            ->with('author')
            ->with('tags')
            ->where('slug', $slug)
            ->where('status_id', 1)
            ->first();
    }

    /**
     * Create a new page
     *
     * @param array  Data to create a new object
     * @return boolean
     */
    public function create(array $data)
    {
        if (!$this->pages->create($data)) {
            return false;
        }

        return true;
    }

    /**
     * Update an existing page
     *
     * @param int id of the page
     * @param array  Data to update an page
     * @return boolean
     */
    public function update($id, array $data)
    {
        unset($data['id']);
        return $this->pages->findFirst("id = '$id'")->update($data);
    }

    /**
     * Delete an existing page
     *
     * @param int id of the page
     * @return boolean
     */
    public function delete($id)
    {
        return $this->pages->findFirst("id = '$id'")->delete($id);
    }

}
