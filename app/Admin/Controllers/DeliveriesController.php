<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Delivery\PhalconDelivery;
use Phasty\Common\Service\Form\DeliveryForm;

class DeliveriesController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new PhalconDelivery();
		$this->form = new DeliveryForm();
	}
}
