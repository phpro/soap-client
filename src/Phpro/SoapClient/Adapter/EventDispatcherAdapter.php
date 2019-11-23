<?php

namespace Phpro\SoapClient\Adapter;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcherAdapter implements \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * For BC with Symfony 4, the $eventName argument is not declared explicitly on the
     * signature of the method. Implementations that are not bound by this BC contraint
     * MUST declare it explicitly, as allowed by PHP.
     *
     * @param object $event The event to pass to the event handlers/listeners
     * @param string|null $eventName The name of the event to dispatch. If not supplied,
     *                               the class of $event should be used instead.
     *
     * @return object The passed $event MUST be returned
     */
    public function dispatch($event, string $eventName = null)
    {
        return $this->dispatcher->dispatch($eventName, $event);
    }
}