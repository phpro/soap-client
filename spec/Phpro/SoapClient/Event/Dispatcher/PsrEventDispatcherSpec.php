<?php

namespace spec\Phpro\SoapClient\Event\Dispatcher;

use Phpro\SoapClient\Event\Dispatcher\EventDispatcherInterface;
use Phpro\SoapClient\Event\SoapEvent;
use PhpSpec\ObjectBehavior;
use Phpro\SoapClient\Event\Dispatcher\PsrEventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherImplementation;

class PsrEventDispatcherSpec extends ObjectBehavior
{
    public function let(PsrEventDispatcherImplementation $dispatcher): void
    {
        $this->beConstructedWith($dispatcher);
    }

   public function it_is_initializable()
    {
        $this->shouldHaveType(PsrEventDispatcher::class);
    }

    public function it_is_a_soap_event_dispatcher(): void
    {
        $this->shouldImplement(EventDispatcherInterface::class);
    }

    public function it_can_dispatch_events(
        PsrEventDispatcherImplementation $dispatcher,
        SoapEvent $event
    ): void {
        $dispatcher->dispatch($event)->shouldBeCalled();
        $this->dispatch($event, 'doesntmatter')->shouldBe($event);
    }
}
