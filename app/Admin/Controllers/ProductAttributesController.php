<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Products\ProductAttributesRepo;
use Phasty\Common\Service\Form\ProductAttributesForm;

class ProductAttributesController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new ProductAttributesRepo();
		$this->form = new ProductAttributesForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		return $this->errorNotFound('There is no attributes');
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
		return $this->errorNotFound('Attribute not created');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function updateAction($id) {
        $input = (array)$this->request->getJsonRawBody();
        //$this->utils->var_dump($this->form->sanitize($input['attributes']));die;
		if (!$this->repo->update($id, $this->form->sanitize($input['attributes']))) {
			return $this->errorInternalError('Can not update attributes');
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
