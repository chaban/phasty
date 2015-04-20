<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\ResetPasswords;

class ResetPasswordHandler extends Plugin implements CommandHandler
{
    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $resetPassword = ResetPasswords::findFirstByCode($command->code);

        if (!$resetPassword) {
            return $this->response->redirect('index');
        }

        if ($resetPassword->reset <> 'N') {
            return $this->dispatcher->forward(array(
                'controller' => 'session',
                'action' => 'login'
            ));
        }

        $resetPassword->reset = 'Y';

        /**
         * Change the confirmation to 'reset'
         */
        if (!$resetPassword->save()) {

            foreach ($resetPassword->getMessages() as $message) {
                $this->flashSession->error($message);
            }

            return $this->response->redirect('index');
        }

        /**
         * Identity the user in the application
         */
        $this->auth->authUserById($resetPassword->usersId);

        $this->flash->success('Please set new password');
    }

}