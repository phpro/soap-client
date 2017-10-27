<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Middleware\Middleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BasicAuthMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class MiddlewareTest extends TestCase
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var MockHandler
     */
    private $handler;

    /**
     * @var Middleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp()
    {
        $this->handler = new MockHandler([]);
        $this->middleware = new Middleware();
        $stack = new HandlerStack($this->handler);
        $stack->push($this->middleware);
        $this->client = new Client(['handler' => $stack]);
    }

    /**
     * @test
     */
    function it_is_a_middleware()
    {
        $this->assertInstanceOf(MiddlewareInterface::class, $this->middleware);
    }

    /**
     * @test
     */
    function it_has_a_name()
    {
        $this->assertEquals('empty_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_applies_middleware_callbacks()
    {
        $this->handler->append($response = new Response());
        $receivedResponse = $this->client->send($request = new Request('POST', '/', ['User-Agent' => 'no']));

        $sentRequest = $this->handler->getLastRequest();
        $this->assertEquals($request, $sentRequest);
        $this->assertEquals($response, $receivedResponse);
    }
}
