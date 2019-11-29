<?php

namespace Phpro\SoapClient\Event;

/**
 * For backward compatibility with Symfony 4
 */
if (class_exists('Symfony\Contracts\EventDispatcher\Event')) {
    abstract class AbstractEvent extends \Symfony\Contracts\EventDispatcher\Event
    {
    }
} else {
    abstract class AbstractEvent extends \Symfony\Component\EventDispatcher\Event
    {
    }
}
