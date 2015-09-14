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
}
