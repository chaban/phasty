<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\EmailConfirmations;

class ConfirmUserEmailHandler extends Plugin implements CommandHandler
{

    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        if ($this->session->has('auth')) {
            return $this->response->redirect('index');
        }

        $confirmation = EmailConfirmations::findFirstByCode($command->code);

        if (!$confirmation) {
            return $this->response->redirect('index');
        }

        if ($confirmation->confirmed <> 'N') {
            return $this->dispatcher->forward(array(
                'controller' => 'session',
                'action' => 'login'
            ));
        }

        $confirmation->confirmed = 'Y';

        $confirmation->user->confirmed = 'Y';

        /**
         * Change the confirmation to 'confirmed' and update the user to 'active'
         */
        if (!$confirmation->save()) {

            foreach ($confirmation->getMessages() as $message) {
                $this->flashSession->error($message);
            }

            return $this->response->redirect('index');
        }

        /**
         * Identity the user in the application
         */
        $this->auth->authUserById($confirmation->user->id);

        /**
         * Check if the user must change his/her password
         */
        if ($confirmation->user->mustChangePassword == 'Y') {

            $this->flashSession->success('Register successfully confirmed. Now, please, enter and remember your password');

            return $this->response->redirect("profile/index/changePassword");
        }
        $this->flashSession->success('Registration successfully confirmed.');
    }

}