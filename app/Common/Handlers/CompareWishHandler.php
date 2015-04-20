<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;

class CompareWishHandler extends Plugin implements CommandHandler
{
    protected $profile;

    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $this->profile = $command->profile;
        //$list = $command->what . 'List';
        if ($command->action == 'add') {
            $this->add($command);
        } else {
            $this->remove($command);
        }
    }

    protected function add($command)
    {
        $productIds = [];
        $list = $command->what . 'List';
        if ($this->profile) {
            if ($this->profile->{$list}) {
                $productIds = json_decode($this->profile->{$list});
            }
            array_push($productIds, $command->productId);
            $productIds = array_unique($productIds);
            $this->profile->{$list} = json_encode($productIds);
            $this->profile->update();
        } else {
            $productIds[] = $command->productId;
        }
        if ($this->session->has($list) && is_array($this->session->get($list))) {
            $productIds = array_unique(array_merge($productIds, $this->session->get($list)));
        }
        $this->session->set($list, $productIds);
    }

    protected function remove($command)
    {
        $productIds = [];
        $list = $command->what . 'List';
        if ($this->session->has($list) && is_array($this->session->get($list))) {
            $productIds = $this->session->get($list);
        }
        if ($this->profile && $this->profile->{$list}) {
            $productIds = array_unique(array_merge($productIds, json_decode($this->profile->{$list})));
            if (($key = array_search($command->productId, $productIds))) {
                array_splice($productIds, $key, 1);
            }
            if (is_array($productIds) && !empty(array_filter($productIds))) {
                $this->profile->{$list} = json_encode($productIds);
                $this->session->set($list, $productIds);
            } else {
                $this->profile->{$list} = null;
                $this->session->remove($list);
            }
            $this->profile->update();
        } else {
            if (($key = array_search($command->productId, $productIds))) {
                array_splice($productIds, $key, 1);
            }
            if (is_array($productIds) && !empty(array_filter($productIds))) {
                $this->session->set($list, $productIds);
            } else {
                $this->session->remove($list);
            }
        }
    }

}