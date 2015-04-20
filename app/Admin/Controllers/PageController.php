<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Models\Pages;
use Phasty\Common\Repo\Pages\PhalconPages;
use Phalcon\Http\Response;
use Phasty\Common\Service\Form\PagesForm;
//use Phasty\Service\Transformer\PageTransformer;

class PageController extends ControllerBase
{

    protected $repo;
    protected $formData;

    protected function initialize()
    {
        $this->repo = new PhalconPages();
        $this->formData = new PagesForm();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function indexAction()
    {
        $pages = $this->repo->byPage($this->request->getQuery());
        if (!$pages) {
            return $this->errorNotFound('There is no pages');
        }
        return $this->apiOk($pages);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function showAction($id)
    {
        $page = $this->repo->byId($id);
        if (!$page) {
            return $this->errorNotFound('Page not found');
        }

        return $this->apiOk($page);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function storeAction()
    {
        $input = (array)$this->request->getJsonRawBody();
        if(($message = $this->formData->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }
        if($this->repo->create($input)) {
            return $this->apiOk(['message' => "page successfully created"]);
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
        if(($message = $this->formData->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }

        if($this->repo->update($id, $input)) {
            return $this->apiOk(['message' => "page $id updated"]);
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
            return $this->apiOk(['message' => "page $id creted"]);
        }else{
            return $this->errorNotFound();
        }
    }

}
