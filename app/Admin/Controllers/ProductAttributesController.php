<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Products\ProductAttributesRepo;
use Phasty\Common\Service\Form\AttributesForm;

class ProductAttributesController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new ProductAttributesRepo();
		$this->form = new AttributesForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$attributes = $this->repo->all();
		if (!$attributes) {
			return $this->errorNotFound('There is no attributes');
		}
		return $this->apiOk($attributes);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$result = $this->repo->byId($id);
		if (!$result) {
			return $this->errorNotFound('Attribute not found');
		}

		return $this->apiOk($result);
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
			return $this->errorNotFound('Attribute not created');
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
        /*if(($message = $this->form->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }*/
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
			return $this->errorNotFound('Attribute not found');
		}
		return $this->apiOk(['message' => "product attribute  $id deleted"]);
	}

}
