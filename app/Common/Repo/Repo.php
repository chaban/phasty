<?php namespace Phasty\Common\Repo;

use Phalcon\Mvc\User\Plugin;

abstract class Repo extends Plugin {
    /**
     * Create a new resource
     *
     * @param array  Data to create a new object
     * @return boolean
     */
    public function create(array $data)
    {
        return $this->model->save($data, $this->model->getWhiteList());
    }

    /**
     * Update an existing resource
     *
     * @param int id of the page
     * @param array  Data to update an page
     * @return boolean
     */
    public function update($id, array $data)
    {
        return $this->model->findFirst(["id = '$id'"])->update($data, $this->model->getWhiteList());
    }

    /**
     * Delete an existing resource
     *
     * @param int id of the resource
     * @return boolean
     */
    public function delete($id)
    {
        return $this->model->findFirst(["id = '$id'"])->delete($id);
    }
}