<?php namespace Phasty\Common\Repo\Payments;

use Phasty\Common\Models\Currencies;
use Phasty\Common\Models\PaymentCurrency;
use Phasty\Common\Models\Payments;

class PhalconPayments {

	protected $model;

	// Class expects an Eloquent model
	public function __construct() {
		$this->model = new Payments();
	}

    /**
     * Get all resources
     *
     *
     * @return StdClass Object
     */
    public function all() {
        $payments = $this->model->find(['columns' => 'id, name, paymentSystem, position']);
        if (!$payments) {
            return false;
        }
        $result = new \StdClass();
        $result->payments = $payments->toArray();
        return $result;
    }

	/**
	 * Retrieve payments by id
	 * regardless of status
	 *
	 * @param  int $id payments ID
	 * @return stdObject object of payments information
	 */
	public function byId($id) {
		$currencies = Currencies::find(['columns' => 'id, name']);
		$payment = $this->model->findFirst("id = '$id'");
		if (!$payment || !$currencies) {
			return false;
		}
		$result = new \StdClass();
		$c['currencies'] = $currencies->toArray();
		$currency_ids = [];
		foreach ($payment->currencies as $currency) {
			$currency_ids[] = $currency->id;
		}
		$c['currencyIds'] = $currency_ids;
		$result->payment = array_merge($payment->toArray(), $c);
		return $result;
	}

	/**
	 * Create a new payments
	 *
	 * @param array  Data to create a new object
	 * @return boolean
	 */
	public function create(array $data) {
        if(!$this->model->create($data, $this->model->getWhiteList())) {
            return false;
        }
        $payment = $this->model->findFirst(['order' => 'id desc']);
        if (count($data['currencyIds'])) {
            foreach($data['currencyIds'] as $currencyId){
                $temp = new PaymentCurrency();
                $temp->paymentId = $payment->id;
                $temp->currencyId = $currencyId;
                $temp->save();
            }
        }
        return true;
	}

	/**
	 * Update an existing payments
	 *
	 * @param int id of the payments
	 * @param array  Data to update an payments
	 * @return boolean
	 */
	public function update($id, array $data) {
        $payment = $this->model->findFirst("id = '$id'");
        if (!$payment) {
            return false;
        }
        if (!$payment->update($data, $this->model->getWhiteList())) {
            return false;
        }
        if (count($data['currencyIds'])) {
            $pC = PaymentCurrency::find("paymentId = '$id'");
            foreach($pC as $temp){
                $temp->delete();
            }
            foreach($data['currencyIds'] as $currencyId){
                $temp = new PaymentCurrency();
                $temp->paymentId = $payment->id;
                $temp->currencyId = $currencyId;
                $temp->save();
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
