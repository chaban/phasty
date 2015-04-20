<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Discount\PhalconDiscount;
use Phasty\Common\Service\Form\DiscountForm;

class DiscountsController extends ControllerBase {

	protected $repo;
	protected $form;

	public function initialize() {
		$this->repo = new PhalconDiscount();
		$this->form = new DiscountForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$discounts = $this->repo->all();
		if (!$discounts) {
			return $this->errorNotFound('There is no such discount');
		}
		return $this->apiOk($discounts);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$discount = $this->repo->byId($id);
		if (!$discount) {
			return $this->errorNotFound('Discount not found');
		}

		return $this->apiOk($discount);
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
            return $this->errorNotFound('Discount not found');
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
            return $this->errorNotFound('Discount not found');
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
			return $this->errorNotFound('Discount not found');
		}
		return $this->apiOk(['message' => "discount method with $id deleted"]);
	}

}
