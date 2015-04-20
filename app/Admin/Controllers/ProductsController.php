<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Products\ProductsRepo;
use Phalcon\Http\Response;
use Phasty\Common\Service\Form\ProductsForm;

class ProductsController extends ControllerBase
{

    protected $repo;
    protected $form;

    protected function initialize()
    {
        $this->repo = new ProductsRepo();
        $this->form = new ProductsForm();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function indexAction()
    {
        $resources = $this->repo->byPage($this->request->getQuery());
        if (!$resources) {
            return $this->errorNotFound('There is no products');
        }
        return $this->apiOk($resources);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function showAction($id)
    {
        $resource = $this->repo->byId($id);
        if (!$resource) {
            return $this->errorNotFound('Product not found');
        }

        return $this->apiOk($resource);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function storeAction()
    {
        $input = (array)$this->request->getJsonRawBody();
        if(($message = $this->form->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }
        if($this->repo->create($input)) {
            return $this->apiOk(['message' => "product successfully created"]);
        }else{
            return $this->errorNotFound();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function updateAction($id)
    {
        $input = (array)$this->request->getJsonRawBody();
        if(($message = $this->form->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }

        if($this->repo->update($id, $input)) {
            return $this->apiOk(['message' => "product $id updated"]);
        }else{
            return $this->errorNotFound();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroyAction($id)
    {
        if($this->repo->delete($id)) {
            return $this->apiOk(['message' => "product $id deleted"]);
        }else{
            return $this->errorNotFound();
        }
    }

}
