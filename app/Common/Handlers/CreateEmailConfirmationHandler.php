<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phasty\Common\Models\EmailConfirmations;
use utilphp\util;

class CreateEmailConfirmationHandler implements CommandHandler {

    /**
     * @var EmailConfirmationsRepository
     */
    protected $repository;

    /**
     * initialize user repository
     */
    function __construct()
    {
        $this->repository = new EmailConfirmations();
    }

    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        //util::var_dump($command);die;

        $this->repository->create(
            ['userId' => $command->userId]
        );

    }

}