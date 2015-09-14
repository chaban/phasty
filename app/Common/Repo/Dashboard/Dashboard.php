<?php namespace Phasty\Common\Repo\Dashboard;

use Phalcon\Db;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Orders;
use Phasty\Common\Models\Users;
use utilphp\util;

class Dashboard extends Plugin
{

    protected $orders;
    protected $users;

    public function __construct()
    {
        $this->orders = new Orders();
        $this->users = new Users();
    }

    /**
     * Get all resources
     *
     * @return StdClass Object with all dashboard data
     */
    public function all()
    {
        $temp = [];
        $temp[]['users'] = $this->getRecentUsers();
        $temp[]['orders'] = $this->getRecentOrders();
        $temp[]['ordersForYear'] = [$this->getOrdersForYear()];
        $result = new \stdClass();
        $result->dashboards = $temp;
        return $result;
    }

    protected function getOrdersForYear()
    {
        $temp = [];
        $rows = $this->db->fetchAll(
            'SELECT totalPrice, unix_timestamp(updatedAt) as updatedAt FROM orders WHERE (updatedAt >= DATE_SUB(NOW(), INTERVAL ? YEAR)) ORDER BY updatedAt', Db::FETCH_ASSOC, [1]
        );
        $temp['key'] = 'Orders';
        //multiple on 1000 for javascript date object
        foreach ($rows as $row) {
            $temp['values'][] = [((int)$row['updatedAt']) * 1000, (int)$row['totalPrice']];
        }
        return $temp;
    }

    protected function getRecentUsers()
    {
        $users = $this->users->find(['limit' => '5', 'order' => 'id desc']);
        $tempUsers = [];
        foreach ($users as $user) {
            $tempUsers[$user->id]['id'] = $user->id;
            $tempUsers[$user->id]['name'] = $user->name;
            $tempUsers[$user->id]['address'] = $user->profile->address;
            $tempUsers[$user->id]['phone'] = $user->profile->phone;
        }
        return array_values($tempUsers);
    }

    protected function getRecentOrders()
    {
        return $this->orders->find(['limit' => '5', 'order' => 'id desc'])->toArray();
    }

}
