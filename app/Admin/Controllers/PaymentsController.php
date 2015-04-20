<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Payments\PhalconPayments;
use Phasty\Common\Service\Form\PaymentsForm;

class PaymentsController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new PhalconPayments();
		$this->form = new PaymentsForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$payments = $this->repo->all();
		if (!$payments) {
			return $this->errorNotFound('There is no payments');
		}
		return $this->apiOk($payments);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$payment = $this->repo->byId($id);
		if (!$payment) {
			return $this->errorNotFound('Payments not found');
		}

		return $this->apiOk($payment);
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
            return $this->errorNotFound('Attribute not found');
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
            return $this->errorNotFound('Attribute not found');
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
			return $this->errorNotFound('Payments not found');
		}
		return $this->apiOk(['message' => "payment $id deleted"]);
	}

}
