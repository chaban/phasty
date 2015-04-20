<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Users\UsersRepo;
use Phasty\Common\Service\Form\UsersForm;

class UsersController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new UsersRepo();
		$this->form = new UsersForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$users = $this->repo->byPage($this->request->getQuery());
		if (!$users) {
			return $this->errorNotFound('There is no users');
		}
		return $this->apiOk($users);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$user = $this->repo->byId($id);
		if (!$user) {
			return $this->errorNotFound('User not found');
		}

		return $this->apiOk($user);
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
            return $this->errorNotFound('User not found');
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
            return $this->errorNotFound('User not found');
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
            return $this->errorNotFound('User not found');
        }
        return $this->apiOk(['message' => "user $id deleted"]);
	}

}
