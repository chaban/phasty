<?php namespace Phasty\Common\Repo\UserProfile;

use Phalcon\Db\RawValue;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\UserProfile;

class PhalconUserProfile extends Plugin
{

    protected $model;

    // Class expects an model
    public function __construct()
    {
        $this->model = new UserProfile();
    }

    /**
     * Retrieve user profile
     *
     * @param  int $id profile id
     * @return UserProfile() object
     */
    public function byId($id) {
        return $this->model->findFirst(["id = '$id'"]);
    }

    /**
     * Retrieve user profile
     *
     * @param  int $userId user id
     * @return UserProfile() object
     */
    public function byUserId($userId) {
        return $this->model->findFirst(["userId = '$userId'"]);
    }

    /**
     * Update resource
     * @param int
     * @param array  Data
     * @return boolean
     */
    public function update($id, array $data) {
        return $this->model->findFirstById($id)->update($data);
        /*$this->repo->assign([
            'name' => $this->filter->sanitize($data['name'], 'string'),
            'email' => $this->filter->sanitize($data['email'], 'email'),
            'password' => $this->security->hash($data['password'])
        ]);

        return $this->repo;*/
    }

    /**
     * Create a new resource
     *
     * @param array  Data to create a new object
     * @return boolean
     */
    public function create(array $data) {
        $this->model->assign([
            'name' => $this->filter->sanitize($data['name'], 'string'),
            'email' => $this->filter->sanitize($data['email'], 'email'),
            'password' => $this->security->hash($data['password'])
        ]);

        return $this->model;
    }
}