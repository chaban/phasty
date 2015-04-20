<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Users;

class SendConfirmationEmailHandler extends Plugin implements CommandHandler
{

    /**
     * @var UserRepository
     */
    //protected $repository;

    /**
     * initialize user repository
     */

    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $user = Users::findFirstById($command->userId);
        $viewVars = [
            'publicUrl' => $this->config->app->publicUrl,
            'confirmUrl' => 'session/confirmEmail/' . $command->code . '/' . $user->email
        ];
        $message = $this->mailer->createMessageFromView('confirmation', $viewVars, null)
            ->to($user->email, $user->name)
            ->subject('Email confirmation');
        $message->send();
    }

}