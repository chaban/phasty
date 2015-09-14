<?php namespace Phasty\Common\Traits;

use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;

trait AdminAreaAccessTrait
{
    /**
     * Check access permissions to admin area
     * @param Dispatcher $dispatcher
     * @return mixed
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
       //Get the current identity
        $identity = $this->auth->getIdentity();
        //If there is no identity available the user forbidden
        if (!is_array($identity)) {
            $this->flashSession->error('Please login or register first.');
            $dispatcher->setReturnedValue($this->response->redirect('/session/login'));
            return false;
        }

        if ($identity['role'] == 'guest' || $identity['role'] == 'customer') {
            $dispatcher->setReturnedValue($this->response->redirect('/index'));
            return false;
        }

        $controllerName = $dispatcher->getControllerName();
        $actionName = $dispatcher->getActionName();
        //check permissions
        if (!$this->acl->isAllowed($identity['role'], $controllerName, $actionName)) {
            $dispatcher->setReturnedValue($this->errorForbidden('You have not access to this area'));
            return false;
        }
        
        if(!$this->request->isGet()){
            $authorizationHeader = $this->request->getHeader('Authorization');
            $token = trim(str_replace('Bearer ', '', $authorizationHeader));
            if(!$this->auth->checkJwtToken($token)){
                $dispatcher->setReturnedValue($this->errorForbidden('You have not allowed do this'));
                return false;
            }
        }
    }
}