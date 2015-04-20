<?php namespace Phasty\Common\Repo\Orders;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Deliveries;
use Phasty\Common\Models\Discounts;
use Phasty\Common\Models\Orders;
use Phasty\Common\Models\OrderStatus;
use Phasty\Common\Models\Users;

class OrdersRepo extends Plugin
{

    protected $orders;

    // Class expects an Eloquent model
    public function __construct()
    {
        $this->model = new Orders();
    }

    /**
     * Retrieve order by id
     * regardless of status
     *
     * @param  int $id order ID
     * @return stdObject object of order information
     */
    public function byId($id)
    {
        $order = $this->model->findFirst(["id = '$id'", 'columns' =>
            'id, userId, userName, email, address, phone, deliveryId, statusId, priceWithDelivery,
            discountId, totalPrice, ipAddress, createdAt, updatedAt,deletedAt, userComment, adminComment']);
        $users = Users::find(['columns' => 'id, name']);
        $deliveries = Deliveries::find(['columns' => 'id, name']);
        $status = OrderStatus::find(['columns' => 'id, name']);
        $discounts = Discounts::find(['columns' => 'id, name']);
        if (!$order || !$users || !$deliveries || !$status || !$discounts) {
            return false;
        }
        $temp = [];
        $temp['users'] = $users->toArray();
        $temp['deliveries'] = $deliveries->toArray();
        $temp['statuses'] = $status->toArray();
        $temp['discounts'] = $discounts->toArray();
        $result = new \StdClass();
        $result->order = array_merge($order->toArray(), $temp);
        return $result;
    }

    /**
     * Get paginated orders
     * @param array $params from _GET[]
     *
     * @return StdClass Object with $items and $totalItems for pagination
     */
    public function byPage($params = array())
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
        $result->orders = array();

        $builder = $this->modelsManager->createBuilder()->from('Phasty\Common\Models\Orders');
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

        $orders = $builder->offset($limit * ($pageNumber))
            ->columns(['id, userName, deliveryName, statusName, totalPrice, createdAt, updatedAt'])
            ->limit($limit)
            ->getQuery()
            ->execute();

        if (!$orders) {
            return false;
        }

        $result->orders = $orders->toArray();

        return $result;
    }

    /**
     * Create a new order
     *
     * @param array  Data to create a new object
     * @return boolean
     */
    public function create(array $data)
    {
        $user = Users::findFirstById($data['userId']);
        $delivery = Deliveries::findFirstById($data['deliveryId']);
        $status = OrderStatus::findFirstById($data['statusId']);

        if (!$user || !$delivery || !$status) {
            return false;
        }
        $order = new Orders();
        $order->userName = $user->name;
        $order->statusName = $status->name;
        $order->deliveryName = $delivery->name;

        return $order->save($data, $this->model->getWhiteList());
    }

    /**
     * Update an existing order
     *
     * @param int id of the order
     * @param array  Data to update an order
     * @return boolean
     */
    public function update($id, array $data)
    {
        $order = $this->model->findFirst("id = '$id'");
        if(!$order || !$order->update($data, $this->model->getWhiteList())){
            return false;
        }
        //$order = $this->model->findFirst("id = '$id'");
        $order->refresh();
        $order->userName = $order->user->name;
        $order->deliveryName = $order->delivery->name;
        $order->statusName = $order->status->name;
        $order->totalPrice = $order->calculateTotalPrice();
        $order->priceWithDelivery = $order->totalPrice + $order->getDeliveryPrice();
        return $order->update();
    }

}
