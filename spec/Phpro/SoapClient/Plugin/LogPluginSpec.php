<?php

namespace spec\Phpro\SoapClient\Plugin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogPluginSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\SoapClient\Plugin\LogPlugin');
    }

    function it_should_be_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }
}
