<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Delivery\PhalconDelivery;
use Phasty\Common\Service\Form\DeliveryForm;

class DeliveriesController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new PhalconDelivery();
		$this->form = new DeliveryForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$delivery = $this->repo->all();
		if (!$delivery) {
			return $this->errorNotFound('There is no such delivery method');
		}
		return $this->apiOk($delivery);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$delivery = $this->repo->byId($id);
		if (!$delivery) {
			return $this->errorNotFound('Delivery method not found');
		}

		return $this->apiOk($delivery);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeAction() {
        $input = (array)$this->request->getJsonRawBody();
        if(($message = $this->form->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }
        if (!$this->repo->create($input)) {
            return $this->errorNotFound('Delivery not found');
        }
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function updateAction($id) {
        $input = (array)$this->request->getJsonRawBody();
        if(($message = $this->form->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }
        if (!$this->repo->update($id, $input)) {
            return $this->errorNotFound('Currency not found');
        }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function destroyAction($id) {
		if (!$this->repo->delete($id)) {
			return $this->errorNotFound('Delivery not found');
		}
		return $this->apiOk(['message' => "delivery method with $id deleted"]);
	}

}
