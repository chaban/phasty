<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Repo\Users\UsersRepo;
use Phasty\Common\Models\Users;

class RegisterUserHandler extends Plugin implements CommandHandler {

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * initialize user repository
     */
    function __construct()
    {
        $this->repository = new UsersRepo();
    }

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

        if(Users::findFirstByEmail($command->email)){
            $this->flashSession->notice('User with such email already registered');
            return $this->response->redirect('session/login');
        }

        $this->repository->create(
            ['name' => $command->name, 'email' => $command->email, 'password' => $command->password]
        );

        $this->flashSession->notice("Please check your email, and complete registration.");
    }

}