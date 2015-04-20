<?php namespace Phalcon\Commander;

class BasicCommandTranslator implements CommandTranslator {

    /**
     * Translate a command to its handler counterpart
     *
     * @param $command
     * @return mixed
     * @throws HandlerNotRegisteredException
     */
    public function toCommandHandler($command)
    {
        $commandClass = get_class($command);
        //$handler = substr_replace($commandClass, 'Handler', strrpos($commandClass, 'Command'));
        $handler = str_replace('Command', 'Handler', $commandClass);

        if ( ! class_exists($handler))
        {
            $message = "Command handler [$handler] does not exist.";

            throw new HandlerNotRegisteredException($message);
        }

        return $handler;
    }

}
