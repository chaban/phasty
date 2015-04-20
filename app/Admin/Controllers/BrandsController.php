<?php namespace Phasty\Admin\Controllers;


use Phasty\Common\Repo\Brands\PhalconBrands;
use Phasty\Common\Service\Form\BrandsForm;

class BrandsController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new PhalconBrands();
		$this->form = new BrandsForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$brands = $this->repo->all();
		if (!$brands) {
			return $this->errorNotFound('There is no brands');
		}
		return $this->apiOk($brands);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$brand = $this->repo->byId($id);
		if (!$brand) {
			return $this->errorNotFound('Brand not found');
		}

		return $this->apiOk($brand);
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
			return $this->errorNotFound('Brand not created');
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
			return $this->errorNotFound('Brand not found');
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
			return $this->errorNotFound('Brand not found');
		}
		return $this->apiOk(['message' => "brand $id deleted"]);
	}

}
