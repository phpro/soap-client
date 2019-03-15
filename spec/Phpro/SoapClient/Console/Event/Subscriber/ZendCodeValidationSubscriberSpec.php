<?php

namespace spec\Phpro\SoapClient\Console\Event\Subscriber;

use Phpro\SoapClient\Console\Event\Subscriber\ZendCodeValidationSubscriber;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ZendCodeValidationSubscriberSpec
 * @package spec\Phpro\SoapClient\Event
 */
class ZendCodeValidationSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ZendCodeValidationSubscriber::class);
    }

    function it_be_an_eventsubsciberinterface()
    {
        $this->shouldBeAnInstanceOf(EventSubscriberInterface::class);
    }

    function it_should_subscibe_to_command_event()
    {
        self::getSubscribedEvents()->shouldContain('onCommand');
        self::getSubscribedEvents()->shouldHaveKey(ConsoleEvents::COMMAND);
    }
}
