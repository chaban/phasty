<?php namespace Phasty\Front\Controllers;

use Phasty\Common\Repo\Currency\CurrencyCache;

class CurrenciesController extends ControllerBase {
	protected $currencies;
	protected function initialize() {
		$this->currencies = (new CurrencyCache())->all();
		parent::initialize();
		$this->tag->setTitle('Currencies | Phasty');
	}

	/**
	 * just forward to catalog
	 */
	public function indexAction() {
		$this->dispatcher->forward([
			'controller' => 'catalog',
			'action' => 'index',
		]);
	}

	public function changeAction($currency = '') {
		if (in_array($currency, array_keys($this->currencies))) {
			$this->session->set('currency', $currency);
		}
		return $this->response->redirect($this->request->getServer('HTTP_REFERER'));
	}

}
