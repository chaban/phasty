<?php namespace Phalcon\Widgets;

use Phalcon\DI\Injectable;

abstract class AbstractWidget extends Injectable {

	public function __construct($config)
	{
		foreach ($config as $property => $value)
		{
			if(property_exists($this, $property))
			{
				$this->$property = $value;
			}
		}
        $this->initialize();
	}

    abstract public function initialize();

	abstract public function run();
}