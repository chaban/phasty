<?php namespace Phasty\Admin\Controllers;

use \Phalcon\Mvc\Controller;
use Phasty\Common\Traits\AdminAreaAccessTrait;
use Phasty\Common\Traits\RestControllerTrait;

/**
 * Class ControllerBase
 * @package Phasty\Http\Controllers\Admin
 */
class ControllerBase extends Controller {

    use AdminAreaAccessTrait;
    use RestControllerTrait;

    const CODE_WRONG_ARGS = 'GEN-FUBARGS';
    const CODE_NOT_FOUND = 'GEN-LIKETHEWIND';
    const CODE_INTERNAL_ERROR = 'GEN-AAAGGH';
    const CODE_UNAUTHORIZED = 'GEN-MAYBGTFO';
    const CODE_FORBIDDEN = 'GEN-GTFO';
    const CODE_INVALID_MIME_TYPE = 'GEN-UMWUT';

    protected function initialize() {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Phalcon\Http\Response
     */
    public function indexAction()
    {
        $resource = $this->repo->all();
        if (!$resource) {
            return $this->errorNotFound('Resource not found');
        }
        return $this->apiOk($resource);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Phalcon\Http\Response
     */
    public function showAction($id)
    {
        $resource = $this->repo->byId($id);
        if (!$resource) {
            return $this->errorNotFound('Resource not found');
        }

        return $this->apiOk($resource);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Phalcon\Http\Response
     */
    public function storeAction()
    {
        $input = (array)$this->request->getJsonRawBody();
        if(($message = $this->form->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }
        if($this->repo->create($input)) {
            return $this->apiOk(['message' => "resource successfully created"]);
        }else{
            return $this->errorInternalError();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return \Phalcon\Http\Response
     */
    public function updateAction($id)
    {
        $input = (array)$this->request->getJsonRawBody();
        if(($message = $this->form->isNotValid($input))){
            return $this->errorWrongArgs($message);
        }

        if($this->repo->update($id, $input)) {
            return $this->apiOk(['message' => "resource $id updated"]);
        }else{
            return $this->errorInternalError();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Phalcon\Http\Response
     */
    public function destroyAction($id)
    {
        if($this->repo->delete($id)) {
            return $this->apiOk(['message' => "resource $id deleted"]);
        }else{
            return $this->errorNotFound();
        }
    }
}
