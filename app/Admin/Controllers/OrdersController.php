<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Orders\OrdersRepo;
use Phasty\Common\Service\Form\OrdersForm;

class OrdersController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new OrdersRepo();
		$this->form = new OrdersForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
        $resources = $this->repo->byPage($this->request->getQuery());
        if (!$resources) {
            return $this->errorNotFound('There is no orders');
        }
        return $this->apiOk($resources);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$order = $this->repo->byId($id);
		if (!$order) {
			return $this->errorNotFound('Order not found');
		}

		return $this->apiOk($order);
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

        if($this->repo->create($input)) {
            return $this->apiOk(['message' => "new order created"]);
        }else{
            return $this->errorNotFound();
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

        if($this->repo->update($id, $input)) {
            return $this->apiOk(['message' => "order $id updated"]);
        }else{
            return $this->errorNotFound();
        }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function destroyAction($id) {

	}

}
