<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Event\Dispatcher;

use Phpro\SoapClient\Event\SoapEvent;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherImplementation;

class PsrEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var PsrEventDispatcherImplementation
     */
    private $dispatcher;

    public function __construct(PsrEventDispatcherImplementation $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @template T of SoapEvent
     * @param T $event
     * @param string|null $name Deprecated : will be removed  in v2.0!
     * @return T
     */
    public function dispatch(SoapEvent $event, string $name = null): SoapEvent
    {
        $this->dispatcher->dispatch($event);
        return $event;
    }
}
