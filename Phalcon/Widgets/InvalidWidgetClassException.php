<?php namespace Phalcon\Widgets;

class InvalidWidgetClassException extends \Exception {

    protected $message = 'Widget class must extend Phalcon\Widgets\AbstractWidget class';
}
