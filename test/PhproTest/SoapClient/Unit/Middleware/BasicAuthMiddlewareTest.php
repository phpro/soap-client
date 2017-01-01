<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Middleware\BasicAuthMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;

/**
 * Class BasicAuthMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class BasicAuthMiddlewareTest extends \PHPUnit_Framework_TestCase
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
     * @var BasicAuthMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp()
    {
        $this->handler = new MockHandler([]);
        $this->middleware = new BasicAuthMiddleware('username', 'password');
        $stack = HandlerStack::create($this->handler);
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
        $this->assertEquals('basic-auth-middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_adds_basic_auth_to_the_request()
    {
        $this->handler->append(new Response());
        $this->client->send(new Request('POST', '/'));
        $sentRequest = $this->handler->getLastRequest();
        $this->assertEquals(
            sprintf('Basic %s', base64_encode('username:password')),
            $sentRequest->getHeader('Authentication')[0]);
    }
}
