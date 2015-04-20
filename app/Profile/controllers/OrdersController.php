<?php namespace Shop\Profile\Controllers;

class OrdersController extends ControllerBase
{

    protected function initialize()
    {
        $this->view->setTemplateAfter('main');
        parent::initialize();
        $this->tag->appendTitle('Мои заказы');
    }

    public function indexAction()
    {
        $numberPage = $this->request->getQuery("page", "int", 1);
        if (!$numberPage or $numberPage <= 0) {
            $numberPage = 1;
        }
        $builder = $this->modelsManager->createBuilder()->from(array('o' => 'Shop\Models\Order'))->where("user_id = ?1", array(1 => $this->identity['id']))
            ->groupBy("o.id")->orderBy("o.created DESC")->getQuery()->execute();
        $pager = new \Phalcon\Paginator\Pager(new \Phalcon\Paginator\Adapter\Model(array(
                "data" => $builder,
                "limit" => 25,
                "page" => (int)$numberPage)),
            array(
                'layoutClass' => 'Phalcon\Paginator\Pager\Layout\Bootstrap',
                'rangeLength' => 5,
                'urlMask' => '?page={%page_number}',
            ));
        $this->view->pager = $pager;
        $this->view->auth = $this->identity;
        $this->view->breadcrumbs = array('Профиль' => '/profile/index', 'Мои заказы');
    }

}
