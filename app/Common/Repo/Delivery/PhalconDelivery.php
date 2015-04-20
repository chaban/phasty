<?php namespace Phasty\Common\Repo\Delivery;

use Phasty\Common\Models\Deliveries;
use Phasty\Common\Models\DeliveryPayment;
use Phasty\Common\Models\Payments;

class PhalconDelivery {

	protected $model;

	// Class expects an Eloquent model
	public function __construct() {
		$this->model = new Deliveries();
	}

	/**
	 * Retrieve delivery by id
	 * regardless of status
	 *
	 * @param  int $id delivery ID
	 * @return stdObject object of delivery information
	 */
	public function byId($id) {
		$payments = Payments::find(['columns' => 'id, name']);
		$delivery = $this->model->findFirst("id = '$id'");
		if (!$delivery || !$payments) {
			return false;
		}
		$result = new \StdClass();
		$c['payments'] = $payments->toArray();
		$payment_ids = [];
		foreach ($delivery->payments as $payment) {
			$payment_ids[] = $payment->id;
		}
		$c['paymentIds'] = $payment_ids;
		$result->delivery = array_merge($delivery->toArray(), $c);
		return $result;
	}

	/**
	 * Get all resources
	 *
	 *
	 * @return StdClass Object
	 */
	public function all() {
		$deliveries = $this->model->find(['column' => 'id, name']);
		if (!$deliveries) {
			return false;
		}
		$result = new \StdClass();
		$result->deliveries = $deliveries->toArray();
		return $result;
	}

	/**
	 * Create a new delivery
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data) {
        if(!$this->model->create($data, $this->model->getWhiteList())) {
			return false;
		}
        $delivery = $this->model->findFirst(['order' => 'id desc']);
        if (count($data['paymentIds'])) {
            foreach($data['paymentIds'] as $paymentId){
                $dP = new DeliveryPayment();
                $dP->deliveryId = $delivery->id;
                $dP->paymentId = $paymentId;
                $dP->save();
            }
        }
		return true;
	}

	/**
	 * Update an existing delivery
	 *
	 * @param int id of the delivery
	 * @param array  Data to update an delivery
	 * @return boolean
	 */
	public function update($id, array $data)
    {
        $delivery = $this->model->findFirst("id = '$id'");
        if (!$delivery) {
            return false;
        }
        if (!$delivery->update($data, $this->model->getWhiteList())) {
            return false;
        }
        if (count($data['paymentIds'])) {
            $deliveryPayments = DeliveryPayment::find("deliveryId = '$id'");
            foreach($deliveryPayments as $temp){
                $temp->delete();
            }
            foreach($data['paymentIds'] as $paymentId){
                $dP = new DeliveryPayment();
                $dP->deliveryId = $delivery->id;
                $dP->paymentId = $paymentId;
                $dP->save();
            }
        }
        return true;
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
