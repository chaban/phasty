<?php namespace Phasty\Common\Traits;

trait FrontControllerTrait {
    public function beforeExecuteRoute()
    {
        if(!$this->session->has('currency')){
            $this->session->set('currency', $this->config->app->currency);
        }
        $forbidAccess = ['profile', 'orders', 'reviews'];
        if (!$this->session->has('auth') && in_array($this->dispatcher->getControllerName(), $forbidAccess)) {
            //return $this->response->redirect('session/login');
            return $this->dispatcher->forward([
                "controller" => "session",
                "action" => "login"
            ]);
        }
    }
}