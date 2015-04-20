<?php namespace Phasty\Common\Repo\Orders;


use Phasty\Common\Models\OrderStatus;

class OrderStatusesRepo {

    protected $model;

    // Class expects an Eloquent model
    public function __construct() {
        $this->model = new OrderStatus();
    }

    /**
     * Retrieve brand by id
     * regardless of status
     *
     * @param  int $id brand ID
     * @return stdObject object of brand information
     */
    public function byId($id) {
        $item = $this->model->findFirst("id = '$id'");
        if (!$item) {
            return false;
        }
        $result = new \StdClass();
        $result->status = $item->toArray();
        return $result;
    }

    /**
     * Get all resources
     *
     *
     * @return StdClass Object
     */
    public function all() {
        $items = $this->model->find(['columns' => 'id, name']);
        if (!$items) {
            return false;
        }
        $result = new \StdClass();
        $result->orderStatuses = $items->toArray();
        return $result;
    }

    /**
     * Create a new brand
     *
     * @param array  Data to create a new object
     * @return boolean
     */
    public function create(array $data) {
        return $this->model->create($data, $this->model->getWhiteList());
    }

    /**
     * Update an existing brand
     *
     * @param int id of the brand
     * @param array  Data to update an brand
     * @return boolean
     */
    public function update($id, array $data) {
        return $this->model->findFirst("id = '$id'")->update($data, $this->model->getWhiteList());
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
