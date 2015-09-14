<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Models\Categories;
use Phasty\Common\Repo\Categories\PhalconCategories;
use Phalcon\Http\Response;
use Phalcon\Commander\CommanderTrait;
use Phasty\Common\Commands\CreateHierarchicalArrayCommand;
use Phasty\Common\Service\Form\CategoryForm;

class CategoriesController extends ControllerBase
{
    use CommanderTrait;

    protected $repo;
    protected $form;

    protected function initialize()
    {
        $this->repo = new PhalconCategories();
        $this->form = new CategoryForm();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function indexAction()
    {
        $result = $this->repo->all();
        if (!$result) {
            return $this->errorNotFound('There is no categories');
        }
        return $this->apiOk($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function showAction($id)
    {
        $result = $this->repo->byId($id);
        if (!$result) {
            return $this->errorNotFound('Page not found');
        }
        return $this->apiOk($result);
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function storeAction(){
        return $this->errorInternalError('this action not supported');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function updateAction($id)
    {
        $input = $this->request->getJsonRawBody();
        if (($message = $this->form->isNotValid(['title' => $input->title]))) {
            return $this->errorWrongArgs($message);
        }
        if (isset($input->children) && ($message = $this->form->isNotValid(['title' => $input->children->title]))) {
            return $this->errorWrongArgs($message);
        }

        if ($this->repo->update($id, $input)) {
            return $this->apiOk(['message' => "category $id updated"]);
        } else {
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
        if ($this->repo->delete($id)) {
            return $this->apiOk(['message' => "category $id deleted"]);
        } else {
            return $this->errorNotFound();
        }
    }

}
