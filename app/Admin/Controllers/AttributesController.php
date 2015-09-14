<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Attributes\PhalconAttributes;
use Phasty\Common\Service\Form\AttributesForm;

class AttributesController extends ControllerBase {

	protected $repo;
	protected $form;

	protected function initialize() {
		$this->repo = new PhalconAttributes();
		$this->form = new AttributesForm();
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Phalcon\Http\Response
     */
    public function indexAction()
    {
        if($categoryId = $this->request->getQuery('categoryId')){
            $resource = $this->repo->all($categoryId);
        }else {
            $resource = $this->repo->all();
        }
        if (!$resource) {
            return $this->errorNotFound('Resource not found');
        }
        return $this->apiOk($resource);
    }
}
