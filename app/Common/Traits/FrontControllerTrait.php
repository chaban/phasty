<?php namespace Phasty\Common\Traits;

trait FrontControllerTrait {
    public function beforeExecuteRoute()
    {
        if(!$this->session->has('currency')){
            $this->session->set('currency', $this->config->app->currency);
        }
        $forbidAccess = ['profile', 'orders', 'reviews', 'checkout'];
        if (!$this->session->has('auth') && in_array($this->dispatcher->getControllerName(), $forbidAccess)) {
            $this->flash->notice('Please login or register');
            return $this->dispatcher->forward([
                "controller" => "session",
                "action" => "login"
            ]);
        }
    }
}