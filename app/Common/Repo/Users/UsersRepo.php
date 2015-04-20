<?php namespace Phasty\Common\Repo\Users;

use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\UserProfile;
use Phasty\Common\Models\Users;

class UsersRepo extends Plugin
{

    protected $model;

    // Class expects an model
    public function __construct()
    {
        $this->model = new Users();
    }

    /**
     * Retrieve user by id
     * regardless of status
     *
     * @param  int $id user ID
     * @return stdObject object of user information
     */
    public function byId($id)
    {
        $user = $this->model->findFirst(["id = '$id'"]);/*,'columns' =>
            'id, name, email, mustChangePassword, banned, confirmed']);*/
        if (!$user) {
            return false;
        }
        $profile = [];
        $profile['id'] = $user->id;
        $profile['name'] = $user->name;
        $profile['email'] = $user->email;
        $profile['mustChangePassword'] = $user->mustChangePassword;
        $profile['banned'] = $user->banned;
        $profile['confirmed'] = $user->confirmed;
        $profile['address'] = $user->profile->address;
        $profile['phone'] = $user->profile->phone;
        $profile['commentsCount'] = $user->profile->commentsCount;
        $profile['createdAt'] = $user->profile->createdAt;
        $profile['lastLogin'] = $user->profile->lastLogin;
        $result = new \StdClass();
        $result->user = $profile;
        return $result;
    }

    /**
     * Get paginated users
     * @param array $params from _GET[]
     *
     * @return StdClass Object with $items and $totalItems for pagination
     */
    public function byPage($params = array())
    {
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $pageNumber = isset($params['page']) ? $params['page'] : 0;
        $orderBy = isset($params['orderBy']) ? $params['orderBy'] : 'id';
        $order = isset($params['order']) ? $params['order'] : 'asc';
        $filters = isset($params['filterByFields']) ? json_decode($params['filterByFields'], true) : null;

        $result = new \StdClass;
        $result->meta = new \StdClass;
        $result->meta->pageNumber = (int)$pageNumber;
        $result->meta->limit = (int)$limit;
        $result->meta->totalItems = 0;
        $result->users = array();

        $builder = $this->modelsManager->createBuilder()->from('Phasty\Common\Models\Users');
        $builder->orderBy("$orderBy  $order");

        if (is_array($filters)) {
            reset($filters);
            $first = key($filters);
            foreach ($filters as $key => $filter) {
                if ($key === $first)
                    $builder->where("$key like :filter:", ['filter' => '%' . $filter . '%']);
                $builder->orWhere("$key like :filter:", ['filter' => '%' . $filter . '%']);
            }
            $result->meta->totalItems = $builder->getQuery()->execute()->count();
        } else {
            $result->meta->totalItems = $this->model->count();
        }

        $users = $builder->offset($limit * ($pageNumber))
            ->columns(['id, name, email, mustChangePassword, confirmed, banned'])
            ->limit($limit)
            ->getQuery()
            ->execute();

        if (!$users) {
            return false;
        }

        $result->users = $users->toArray();

        return $result;
    }

    /**
     * Create a new user
     *
     * @param array  Data to create a new object
     * @return boolean
     */
    public function create(array $data)
    {
        $user = new Users();
        $user->assign([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->security->hash($data['password']),
            'mustChangePassword' => $data['mustChangePassword'],
            'confirmed' => $data['confirmed'],
            'banned' => $data['banned']
        ]);

        if ($user->create()) {
            $profile = new UserProfile();
            $profile->userId = $user->id;
            $profile->address = $data['address'];
            $profile->phone = $data['phone'];
            if ($profile->save())
                return true;
        }

        return false;
    }

    /**
     * Update an existing user
     *
     * @param int id of the user
     * @param array  Data to update an user
     * @return boolean
     */
    public function update($id, array $data)
    {
        $user = $this->model->findFirst("id = '$id'");

        $user->assign([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->security->hash($data['password']),
            'mustChangePassword' => $data['mustChangePassword'],
            'confirmed' => $data['confirmed'],
            'banned' => $data['banned']
        ]);

        $user->profile->address = $data['address'];
        $user->profile->phone = $data['phone'];
        if ($user->save())
            return true;

        return false;
    }

    public function delete($id){
        return $this->model->findFirst("id = '$id'")->delete();
    }

    /*private function from_camel_case($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }*/

}
