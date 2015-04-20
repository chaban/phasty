<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\AttributeGroups\PhalconAttributeGroups;
use Phasty\Common\Service\Form\AttributeGroupsForm;

class AttributeGroupsController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new PhalconAttributeGroups();
		$this->form = new AttributeGroupsForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$groups = $this->repo->all();
		if (!$groups) {
			return $this->errorNotFound('There is no groups');
		}
		return $this->apiOk($groups);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$page = $this->repo->byId($id);
		if (!$page) {
			return $this->errorNotFound('Group not found');
		}

		return $this->apiOk($page);
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
			return $this->errorNotFound('Group not created');
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
			return $this->errorNotFound('Group not found');
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
			return $this->errorNotFound('Group not found');
		}
		return $this->apiOk(['message' => "group of attributes $id deleted"]);
	}

}
