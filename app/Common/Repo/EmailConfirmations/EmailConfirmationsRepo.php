<?php namespace Phalcon\Common\Repo\EmailConfirmations;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\EmailConfirmations;

class EmailConfirmationsRepo extends Plugin{

    protected $repo;

    public function __construct(){
        $this->repo = new EmailConfirmations();
    }
    /**
     * Create a new email confirmation
     *
     * @param array  Data to create a new object
     * @return boolean
     */
    public function create(array $data) {
        $confirmation = new EmailConfirmations();

        $confirmation->assign([
            'userId' => $this->filter->sanitize($data['userId'], 'int')
        ]);

        if($confirmation->save()){
            return true;
        }
        return false;
    }

}