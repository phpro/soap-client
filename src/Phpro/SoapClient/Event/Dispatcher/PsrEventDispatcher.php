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

    public function dispatch(SoapEvent $event, string $name = null): SoapEvent
    {
        $this->dispatcher->dispatch($event);
        return $event;
    }
}
