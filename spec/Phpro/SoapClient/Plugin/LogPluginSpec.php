<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
