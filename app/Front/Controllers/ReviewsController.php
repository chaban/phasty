<?php namespace Phasty\Front\Controllers;

use Phasty\Common\Models\Reviews;

class ReviewsController extends ControllerBase
{
    protected function initialize()
    {
        parent::initialize();
        $this->tag->setTitle('Product Reviews | Phasty');
    }

    /**
     * just forward to catalog
     */
    public function indexAction()
    {
        $this->dispatcher->forward([
            'controller' => 'catalog',
            'action' => 'index'
        ]);
    }

    public function addAction()
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            $auth = $this->session->get('auth');
            $userId = $auth['id'];
            $productId = $this->request->getPost('productId', 'int');
            $content = $this->request->getPost('content', 'string');
            if($content && $productId && $userId) {
                $model = new Reviews();
                $model->userId = $userId;
                $model->productId = $productId;
                $model->content = $content;
                $model->save();
            }
        }
    }

}