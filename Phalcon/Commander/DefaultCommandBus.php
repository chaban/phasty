<?php namespace Phalcon\Commander;

use InvalidArgumentException;
use utilphp\util;

class DefaultCommandBus implements CommandBus {

	/**
	 * @var CommandTranslator
	 */
	protected $commandTranslator;

	/**
	 * List of optional decorators for command bus.
	 *
	 * @var array
	 */
	protected $decorators = [];

	/**
	 * @param CommandTranslator
	 *
	 */
	function __construct($commandTranslator) {
		$this->commandTranslator = $commandTranslator;
	}

	/**
	 * Decorate the command bus with any executable actions.
	 *
	 * @param  string $className
	 * @return mixed
	 */
	public function decorate($className) {
		$this->decorators[] = $className;

		return $this;
	}

	/**
	 * Execute the command
	 *
	 * @param $command
	 * @return mixed
	 */
	public function execute($command) {
		$this->executeDecorators($command);

		$handlerClass = $this->commandTranslator->toCommandHandler($command);
		$handler = new $handlerClass;
		return $handler->handle($command);
	}

	/**
	 * Execute all registered decorators
	 *
	 * @param  object $command
	 * @return null
	 */
	protected function executeDecorators($command) {
		foreach ($this->decorators as $className) {
			//$instance = $this->app->make($className);
			$instance = new $className;

			if (!$instance instanceof CommandBus) {
				$message = 'The class to decorate must be an implementation of Phalcon\Commander\CommandBus';

				throw new InvalidArgumentException($message);
			}

			$instance->execute($command);
		}
	}

}
