<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;

class CreateHierarchicalArrayHandler extends Plugin implements CommandHandler
{
    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        return $this->nestedArray($command->arrayData, count($command->arrayData));
    }

    protected function nestedArray(&$result, $count, $right = 'rgt', $left = 'lft') {
        $new = array();
        if(is_array($result)) {
            while(list($n, $sub) = each($result)) {
                $subId = $sub['id'];
                $new[$subId] = $sub;

                if($sub[$right] - $sub[$left] != 1) {
                    // recurse ($result is manipulated by reference!)
                    $new[$subId]['children'] = $this->nestedArray($result, $count);
                }

                $next_id = key($result);
                if($next_id == $count) {
                    return array_values($new);
                }
            }
        }
        return array_values($new);
    }

    /*protected function nestedArray(&$result, $right = 'rgt', $left = 'lft') {
        $new = array();
        if(is_array($result)) {
            while(list($n, $sub) = each($result)) {
                $subId = $sub['id'];
                $new[$subId] = $sub;

                if($sub[$right] - $sub[$left] != 1) {
                    // recurse ($result is manipulated by reference!)
                    $new[$subId]['children'] = $this->nestedArray($result);
                }

                $next_id = key($result);
                if($next_id && $result[$next_id]['parent_id'] != $sub['parent_id']) {
                    return array_values($new);
                }
            }
        }
        return array_values($new);
    }*/

}