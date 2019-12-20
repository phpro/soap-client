<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Event\Dispatcher;

use Phpro\SoapClient\Event\SoapEvent;

/**
 * This class is a thin layer around PSR-14.
 * It is added for BC with Symfony 3 (and the old named parameters) whilst making it possible to upgrade to PSR14.
 * An event is forced to always extend from SoapEvent.
 */
interface EventDispatcherInterface
{
    /**
     * @param SoapEvent   $event
     * @param string|null $name Deprecated : will be removed  in v2.0!
     *
     * @return SoapEvent
     */
    public function dispatch(SoapEvent $event, string $name = null): SoapEvent;
}
