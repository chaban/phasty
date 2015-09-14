<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Discount\PhalconDiscount;
use Phasty\Common\Service\Form\DiscountForm;

class DiscountsController extends ControllerBase {

	protected $repo;
	protected $form;

	public function initialize() {
		$this->repo = new PhalconDiscount();
		$this->form = new DiscountForm();
	}
}
