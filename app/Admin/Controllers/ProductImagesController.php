<?php namespace Phasty\Admin\Controllers;

use Phasty\Common\Repo\Products\ProductImagesRepo;
use Phalcon\Http\Response;

class ProductImagesController extends ControllerBase
{

    protected $repo;

    protected function initialize()
    {
        $this->repo = new ProductImagesRepo();
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
            return $this->errorNotFound('Product images not found');
        }
        return $this->apiOk($resource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function storeAction()
    {
        $result = false;
        if($this->request->hasFiles(true)){
            foreach($this->request->getUploadedFiles() as $file) {
                if ($this->repo->save($this->request->get('id'), $file)) {
                    $result = true;
                }
            }
        }
        if($result){
            return $this->apiOk(['message' => 'image saved']);
        }
        return $this->errorInternalError();
    }

    /**
     * Remove the specified image.
     *
     * @param  int $id
     * @return Response
     */
    public function updateAction($id)
    {
        $input = (array)$this->request->getJsonRawBody();
        if($this->repo->delete($id, $input)) {
            return $this->apiOk(['message' => "image for product deleted"]);
        }else{
            return $this->errorNotFound();
        }
    }

}
