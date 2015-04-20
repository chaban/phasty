<?php namespace Phasty\Front\Controllers;

use Phalcon\Mvc\Controller;
use Phasty\Common\Traits\FrontControllerTrait;

/**
 * Class ControllerBase
 * @package Phasty\Admin\Controllers
 */
class ControllerBase extends Controller
{
    use FrontControllerTrait;

    protected function initialize()
    {
        $this->tag->setTitle('Phasty e-commerce');
    }

    /**
     * @param array $value
     * @return string
     */
    protected function jsonEncode($value = null)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
