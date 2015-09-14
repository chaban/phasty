<?php namespace Phasty\Common\Repo\Products;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Brands;
use Phasty\Common\Models\Categories;
use Phasty\Common\Models\Products;

class ProductsRepo extends Plugin
{

    protected $model;

    // Class expects an model
    public function __construct()
    {
        $this->model = new Products();
    }

    /**
     * Retrieve product by id
     * regardless of status
     *
     * @param  int $id product ID
     * @return stdObject object of product information
     */
    public function byId($id)
    {
        $brands = Brands::find(['columns' => 'id, name']);
        $categories = Categories::find(['id > 1', 'columns' => 'id, title']);
        $product = $this->model->findFirst(["id = '$id'", 'columns' =>
            'name, fullDescription, categoryId, brandId, price, quantity, availability,
             autoDecreaseQuantity, active']);
        if (!$product || !$brands || !$categories) {
            return false;
        }
        $temp = [];
        $temp['brands'] = $brands->toArray();
        $temp['categories'] = $categories->toArray();
        $result = new \StdClass();
        $result->product = array_merge($product->toArray(), $temp);
        return $result;
    }

    /**
     * Get all resources
     *
     *
     * @return StdClass Object
     */
    public function all() {
        $products = $this->model->find();
        if (!$products) {
            return false;
        }
        $temp = [];
        foreach($products as $key => $product){
            $temp[$key]['id'] = $product->id;
            $temp[$key]['name'] = $product->name;
            $temp[$key]['price'] = $product->price;
            $temp[$key]['category'] = $product->category->title;
            $temp[$key]['brand'] = $product->brand->name;
            $temp[$key]['quantity'] = $product->quantity;
            $temp[$key]['availability'] = $product->availability;
            $temp[$key]['rating'] = $product->rating;
            $temp[$key]['active'] = $product->active;
        }
        $result = new \StdClass();
        $result->products = $temp;
        return $result;
    }

    /**
     * Get paginated products
     * @param array $params from _GET[]
     *
     * @return StdClass Object with $items and $totalItems for pagination
     */
    /*public function byPage($params = array())
    {
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $pageNumber = isset($params['page']) ? $params['page'] : 0;
        $orderBy = isset($params['orderBy']) ? $params['orderBy'] : 'id';
        $order = isset($params['order']) ? $params['order'] : 'asc';
        $filters = isset($params['filterByFields']) ? json_decode($params['filterByFields'], true) : null;

        $result = new \StdClass;
        $result->meta = new \StdClass;
        $result->meta->pageNumber = (int)$pageNumber;
        $result->meta->limit = (int)$limit;
        $result->meta->totalItems = 0;
        $result->products = array();

        $builder = $this->modelsManager->createBuilder()->from('Phasty\Common\Models\Products');
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
            $result->meta->totalItems = $this->model->count();
        }

        $products = $builder->offset($limit * ($pageNumber))
            ->columns(['id, name, price, rating, viewsCount, addedToCartCount, quantity, availability, active'])
            ->limit($limit)
            ->getQuery()
            ->execute();

        if (!$products) {
            return false;
        }

        $result->products = $products->toArray();

        return $result;
    } */

    /**
     * Create a new product
     *
     * @param array  Data to create a new object
     * @return boolean
     */
    public function create(array $data)
    {
        return $this->model->save($data, $this->model->getWhiteList());
    }

    /**
     * Update an existing product
     *
     * @param int id of the product
     * @param array  Data to update an product
     * @return boolean
     */
    public function update($id, array $data)
    {
        return $this->model->findFirst("id = '$id'")->update($data, $this->model->getWhiteList());
    }

    public function delete($id)
    {
        return $this->model->findFirst("id = '$id'")->delete();
    }

    /*private function from_camel_case($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }*/

}
