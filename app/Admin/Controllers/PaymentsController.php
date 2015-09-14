<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Payments\PhalconPayments;
use Phasty\Common\Service\Form\PaymentsForm;

class PaymentsController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new PhalconPayments();
		$this->form = new PaymentsForm();
	}
}
