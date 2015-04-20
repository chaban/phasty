<?php
namespace Shop\Profile\Controllers;
class ControllerBase extends \Phalcon\Mvc\Controller
{
    public $identity = null;

    protected function initialize()
    {
        $this->identity = $this->auth->getIdentity();
        $this->tag->prependTitle('Профиль | ');
    }

    public function beforeExecuteRoute()
    {
        $auth = $this->auth->getIdentity();
        //If there is no identity available the user is redirected
        if (!is_array($auth)) {
            $this->flashSession->notice("Пожалуйста войдите или зарегистрируйтесь.");
            return $this->response->redirect('session/login');
        }
    }
}
