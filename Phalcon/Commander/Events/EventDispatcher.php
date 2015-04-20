<?php namespace Phalcon\Commander\Events;

use Phalcon\Commander\Events\Contracts\Dispatcher as DispatcherInterface;
use Phalcon\Mvc\User\Plugin;

class EventDispatcher extends Plugin implements DispatcherInterface {

    /**
     * The Dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $event;

    /**
     * The writer instance.
     *
     * @var Writer
     */
    //protected $log;

    /**
     * Create a new EventDispatcher instance.
     *
     * @param Dispatcher $event
     *
     */
    function __construct(Dispatcher $event)//, Writer $log)
    {
        $this->event = $event;
        //$this->log = $log;
    }

    /**
     * Dispatch all raised events.
     *
     * @param array $events
     */
    public function dispatch(array $events)
    {
        $this->utils->var_dump($events);die;
        /*foreach($events as $event)
        {
            $eventName = $this->getEventName($event);

            $this->event->fire($eventName, $event);

            $this->logger->log("{$eventName} was fired.");
        }*/
    }

    /**
     * Make the fired event name look more object-oriented.
     *
     * @param $event
     * @return string
     */
    protected function getEventName($event)
    {
        return str_replace('\\', '.', get_class($event));
    }

} 