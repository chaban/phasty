<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\ResetPasswords;
use Phasty\Common\Models\Users;

class ForgotPasswordHandler extends Plugin implements CommandHandler
{
    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $user = Users::findFirstByEmail($command->email);
        if (!$user) {
            $this->flashSession->error('There are no such a user.');
            return $this->response->redirect('session/login');
        } else {
            $resetPassword = new ResetPasswords();
            $resetPassword->usersId = $user->id;
            if ($resetPassword->save()) {
                $viewVars = [
                    'publicUrl' => $this->config->app->publicUrl,
                    'resetUrl' => 'session/resetPassword/' . $resetPassword->code . '/' . $user->email
                ];
                $message = $this->mailer->createMessageFromView('reset', $viewVars, null)
                    ->to($user->email, $user->name)
                    ->subject('Reset password');
                $message->send();
                $this->flashSession->success('Check pleas your email. Follow link in email and change password');
                return $this->response->redirect('session/login');
            } else {
                foreach ($resetPassword->getMessages() as $message) {
                    $this->flashSession->error($message);
                }
                return $this->response->redirect('session/login');
            }
        }
    }

}