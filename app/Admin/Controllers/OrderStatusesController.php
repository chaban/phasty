<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Orders\OrderStatusesRepo;
use Phasty\Common\Service\Form\OrderStatusForm;

class OrderStatusesController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new OrderStatusesRepo();
		$this->form = new OrderStatusForm();
	}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function indexAction() {
        $resources = $this->repo->all();
        if (!$resources) {
            return $this->errorNotFound('There is no order statuses');
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
			return $this->errorNotFound('Order status not found');
		}

		return $this->apiOk($order);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeAction() {
		$input = Input::all();
		try
		{
			$this->ordersForm->validate($input);
		} catch (FormValidationException $e) {
			return $this->errorWrongArgs($e->getErrors()->toArray());
		}
		$this->orders->create($input);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function updateAction($id) {
		$input = Input::all();
		try
		{
			$this->ordersForm->validate($input);
		} catch (FormValidationException $e) {
			return $this->errorWrongArgs($e->getErrors()->toArray());
		}
		$this->orders->update($id, $input);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function destroyAction($id) {
		Orders::destroy($id);
		return Response::json(['message' => "order $id deleted"]);
	}

}
