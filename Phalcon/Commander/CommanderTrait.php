<?php namespace Phalcon\Commander;

use InvalidArgumentException;
use ReflectionClass;

trait CommanderTrait {

	/**
	 * Execute the command.
	 *
	 * @param  string $command
	 * @param  array $input
	 * @param  array $decorators
	 * @return mixed
	 */
	protected function execute($command, array $input = null, $decorators = []) {
		$input = $input ?: $this->request->getPost();

		$command = $this->mapInputToCommand($command, $input);

		// If any decorators are passed, we'll filter through and register them
		// with the CommandBus, so that they are executed first.
		foreach ($decorators as $decorator) {
			$this->commandBus->decorate($decorator);
		}

		return $this->commandBus->execute($command);
	}

	/**
	 * Map an array of input to a command's properties.
	 *
	 * @param  string $command
	 * @param  array $input
	 * @throws InvalidArgumentException
	 * @author Taylor Otwell
	 *
	 * @return mixed
	 */
	protected function mapInputToCommand($command, array $input) {
		$dependencies = [];

		$class = new ReflectionClass($command);

		foreach ($class->getConstructor()->getParameters() as $parameter) {
			$name = $parameter->getName();

			if (array_key_exists($name, $input)) {
				$dependencies[] = $input[$name];
			} elseif ($parameter->isDefaultValueAvailable()) {
				$dependencies[] = $parameter->getDefaultValue();
			} else {
				throw new InvalidArgumentException("Unable to map input to command: {$name}");
			}
		}

		return $class->newInstanceArgs($dependencies);
	}

}
