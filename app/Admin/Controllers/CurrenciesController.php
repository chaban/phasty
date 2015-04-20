<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Currency\PhalconCurrency;
use Phasty\Common\Service\Form\CurrencyForm;

class CurrenciesController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new PhalconCurrency();
		$this->form = new CurrencyForm();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$currencies = $this->repo->all();
		if (!$currencies) {
			return $this->errorNotFound('There is no currency');
		}
		return $this->apiOk($currencies);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function showAction($id) {
		$currency = $this->repo->byId($id);
		if (!$currency) {
			return $this->errorNotFound('Currency not found');
		}

		return $this->apiOk($currency);
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
            return $this->errorNotFound('Currency not found');
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
			return $this->errorNotFound('Currency not found');
		}
		return $this->apiOk(['message' => "currency $id deleted"]);
	}

}
