<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Caller;

use Phpro\SoapClient\Caller\EngineCaller;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Type\MultiArgumentRequest;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Soap\Engine\Engine;

class EngineCallerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy&Engine
     */
    private ObjectProphecy $engine;

    private EngineCaller $caller;

    protected function setUp(): void
    {
        $this->engine = $this->prophesize(Engine::class);
        $this->caller = new EngineCaller($this->engine->reveal());
    }

    /** @test */
    public function it_can_handle_simple_request_and_response(): void
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $response = $this->prophesize(ResultInterface::class)->reveal();

        $this->engine->request('method', [$request])->willReturn($response);
        $result = ($this->caller)('method', $request);

        self::assertSame($response, $result);
    }

    /** @test */
    public function it_can_handle_multi_argument_request(): void
    {
        $request = new MultiArgumentRequest($args = [1, 2, 3]);
        $response = $this->prophesize(ResultInterface::class)->reveal();

        $this->engine->request('method', $args)->willReturn($response);
        $result = ($this->caller)('method', $request);

        self::assertSame($response, $result);
    }

    /** @test */
    public function it_can_handle_result_providers(): void
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $resultProvider = $this->prophesize(ResultProviderInterface::class);
        $response = $this->prophesize(ResultInterface::class)->reveal();
        $resultProvider->getResult()->willReturn($response);

        $this->engine->request('method', [$request])->willReturn($resultProvider->reveal());
        $result = ($this->caller)('method', $request);

        self::assertSame($response, $result);
    }

    /** @test */
    public function it_can_handle_mixed_results(): void
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();

        $this->engine->request('method', [$request])->willReturn(132);
        $result = ($this->caller)('method', $request);

        self::assertInstanceOf(MixedResult::class, $result);
        self::assertSame(132, $result->getResult());
    }

    /** @test */
    public function it_can_handle_exceptions(): void
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $this->engine->request('method', [$request])->willThrow(new \Exception('noooo'));

        $this->expectException(SoapException::class);
        $this->expectExceptionMessage('noooo');
        ($this->caller)('method', $request);
    }
}
