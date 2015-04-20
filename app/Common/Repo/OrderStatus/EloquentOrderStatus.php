<?php namespace Phasty\Common\Repo\OrderStatus;

use Illuminate\Database\Eloquent\Model;

class EloquentOrderStatus implements OrderStatusInterface {

	protected $orderStatus;

	// Class expects an Eloquent model
	public function __construct(Model $orderStatus) {
		$this->orderStatus = $orderStatus;
	}

	/**
	 * Retrieve order by id
	 * regardless of status
	 *
	 * @param  int $id order ID
	 * @return stdObject object of order information
	 */
	public function byId($id) {
		$order = $this->orderStatus->find($id);
		if (!$order) {
			return false;
		}
		$result = new \StdClass();
		$result->order = $order->toArray();
		return $result;
	}

	/**
	 * Retrieve all resources
	 * regardless of status
	 *
	 * @return stdObject object of resource information
	 */
	public function getAll() {
		$orderStatus = $this->orderStatus->all();
		if (!$orderStatus) {
			return false;
		}
		$result = new \StdClass();
		$result->orderStatus = $orderStatus->toArray();
		return $result;
	}

	/**
	 * Create a new order
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data) {
		// Create the order
		$order = $this->orderStatus->create($data);

		if (!$order) {
			return false;
		}

		return true;
	}

	/**
	 * Update an existing order
	 *
	 * @param int id of the order
	 * @param array  Data to update an order
	 * @return boolean
	 */
	public function update($id, array $data) {
		return $this->orderStatus->find($id)->update($data);
	}

}
