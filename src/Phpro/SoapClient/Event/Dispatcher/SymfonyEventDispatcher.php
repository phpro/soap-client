<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Event\Dispatcher;

use Phpro\SoapClient\Event\SoapEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyLegacyEventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherContract;

/**
 * @deprecated This one is added for BC compatibility between Symfony 3, 4 and 5.
 * It will be removed  in v2.0. We recommend using the PsrEventDispatcher.
 * Since Symfony EventDispatcher is compatible with PSR-14, you can still use the Symfony dispatcher.
 */
class SymfonyEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var SymfonyLegacyEventDispatcher|SymfonyEventDispatcherContract
     */
    private $dispatcher;

    public function __construct($eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @template T of SoapEvent
     * @param T $event
     * @param string|null $eventName Deprecated : will be removed  in v2.0!
     * @return T
     */
    public function dispatch(SoapEvent $event, string $eventName = null): SoapEvent
    {
        $interfacesImplemented = class_implements($this->dispatcher);
        if (in_array(SymfonyEventDispatcherContract::class, $interfacesImplemented, true)) {
            $this->dispatcher->dispatch($event, $eventName);
            return $event;
        }

        /** @phpstan-ignore-next-line */
        $this->dispatcher->dispatch($eventName, $event);

        return $event;
    }
}
