<?php

namespace Phpro\SoapClient\Event;

use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Contracts\EventDispatcher\Event as ContractEvent;

/**
 * For backward compatibility with Symfony 4
 * We'll make this event PSR14 in the future so that we don't rely on the symfony event anymore.
 * TODO : Replace internal event subscribers by listeners
 */
// @codingStandardsIgnoreStart
if (class_exists(ContractEvent::class)) {
    class SoapEvent extends ContractEvent
    {
    }
} else {
    class SoapEvent extends LegacyEvent
    {
    }
}
// @codingStandardsIgnoreEnd
