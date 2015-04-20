<?php namespace Phasty\Admin\Controllers;

use Phalcon\Mvc\View;

class IndexController extends ControllerBase {

	protected function initialize()
	{
		parent::initialize();
		$this->tag->setTitle('Admin area | E-Shopper');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction() {
		$this->view->enable();
		$this->view->setRenderLevel(View::LEVEL_MAIN_LAYOUT);
		//$this->utils->var_dump($this->view);die;
		//return Response::json(['success' => 'success']);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {
		//
	}

}
