<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Caller;

use Phpro\SoapClient\Caller\Caller;
use Phpro\SoapClient\Caller\EventDispatchingCaller;
use Phpro\SoapClient\Event\FaultEvent;
use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Event\ResponseEvent;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatchingCallerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy&EventDispatcherInterface
     */
    private ObjectProphecy $eventDispatcher;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
    }


    /** @test */
    public function it_triggers_events_for_successfull_requests(): void
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $result = $this->prophesize(ResultInterface::class)->reveal();

        $this->eventDispatcher->dispatch(
            $requestEvent = new RequestEvent('method', $request)
        )->shouldBeCalled()->willReturn($requestEvent);
        $this->eventDispatcher->dispatch(
            $responseEvent = new ResponseEvent($requestEvent, $result)
        )->shouldBeCalled()->willReturn($responseEvent);

        $caller = $this->createCaller(function (ObjectProphecy $caller) use ($request, $result) {
            $caller->__invoke('method', $request)->willReturn($result);
        });
        $actual = $caller('method', $request);

        self::assertSame($result, $actual);
    }

    /** @test */
    public function it_triggers_events_for_failing_requests(): void
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $exception = new SoapException('nope');

        $this->eventDispatcher->dispatch(
            $requestEvent = new RequestEvent('method', $request)
        )->shouldBeCalled()->willReturn($requestEvent);
        $this->eventDispatcher->dispatch(
            new FaultEvent($exception, $requestEvent)
        )->shouldBeCalled();

        $caller = $this->createCaller(function (ObjectProphecy $caller) use ($request, $exception) {
            $caller->__invoke('method', $request)->willThrow($exception);
        });

        $this->expectExceptionObject($exception);
        $caller('method', $request);
    }

    private function createCaller(callable $configure): EventDispatchingCaller
    {
        $caller = $this->prophesize(Caller::class);

        $configure($caller);

        return new EventDispatchingCaller(
            $caller->reveal(),
            $this->eventDispatcher->reveal()
        );
    }
}
