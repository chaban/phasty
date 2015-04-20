<?php namespace Phasty\Front\Controllers;

use Phalcon\Commander\CommanderTrait;
use Phasty\Common\Commands\CompareWishCommand;
use Phasty\Common\Models\Products;
use Phasty\Common\Models\UserProfile;

class CompareController extends ControllerBase
{
    use CommanderTrait;
    protected $profile;

    protected function initialize()
    {
        if (is_array($auth = $this->auth->getIdentity())) {
            $this->profile = UserProfile::findFirst(["conditions" => "userId = ?1", "bind" => [1 => $auth['id']]]);
        } else {
            $this->profile = null;
        }
        parent::initialize();
    }

    /**
     * @param string slug
     * @return view
     */
    public function indexAction()
    {
        $this->response->redirect('compare/show/wish');
    }

    public function showAction($what = 'wish'){
        if($what !== 'wish'){
            $what = 'compare';
        }
        $list = $what.'List';
        $this->tag->appendTitle('Compare List');
        $products = null;
        if($this->profile){
            $this->session->set($list, json_decode($this->profile->{$list}));
        }
        if($this->session->has($list) && is_array($this->session->get($list))){
            $products = Products::query()->inWhere('id', $this->session->get($list))->execute();
        }
        $this->view->setVars(['what' => $list, 'products' => $products]);
    }

    public function addOrRemoveAction()
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            $input = [];
            $input['productId'] = $this->request->get('productId', 'int');
            $input['what'] = $this->request->get('what', 'string', 'wish');
            $input['action'] = $this->request->get('action', 'string', 'add');
            $input['profile'] = $this->profile;
            $this->execute(CompareWishCommand::class, $input);
        }
    }

}