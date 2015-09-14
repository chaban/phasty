<?php namespace Phasty\Admin\Controllers;

use Phalcon\Mvc\View;
use Phasty\Common\Repo\Dashboard\Dashboard;

class IndexController extends ControllerBase
{

    protected $repo;

    protected function initialize()
    {
        $this->repo = new Dashboard();
        parent::initialize();
        $this->tag->setTitle('Admin area | Phasty');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->view->enable();
        $this->view->setRenderLevel(View::LEVEL_MAIN_LAYOUT);
    }

    /**
     * return dashboard data.
     *
     * @return Response
     */
    public function dashboardAction()
    {
        $data = null;
        if('yes' == $this->request->getQuery('needToken')){
            $data = new \stdClass();
            $temp = [];
            $temp[]['token'] = $this->auth->getJwtToken();
            $data->dashboards = $temp;
        }else {
            $data = $this->repo->all();
        }
        if (!$data) {
            return $this->errorNotFound('There is no data to show');
        }
        return $this->apiOk($data);
    }

}
