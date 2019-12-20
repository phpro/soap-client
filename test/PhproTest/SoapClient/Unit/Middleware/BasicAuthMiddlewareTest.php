<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\PluginClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Mock\Client;
use Phpro\SoapClient\Middleware\BasicAuthMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BasicAuthMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class BasicAuthMiddlewareTest extends TestCase
{

    /**
     * @var PluginClient
     */
    private $client;

    /**
     * @var Client
     */
    private $mockClient;

    /**
     * @var BasicAuthMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp(): void
    {
        $this->middleware = new BasicAuthMiddleware('username', 'password');
        $this->mockClient = new Client(new GuzzleMessageFactory());
        $this->client = new PluginClient($this->mockClient, [$this->middleware]);
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
        $this->assertEquals('basic_auth_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_adds_basic_auth_to_the_request()
    {
        $this->mockClient->addResponse(new Response());
        $this->client->sendRequest(new Request('POST', '/'));
        $sentRequest = $this->mockClient->getRequests()[0];
        $this->assertEquals(
            sprintf('Basic %s', base64_encode('username:password')),
            $sentRequest->getHeader('Authorization')[0]);
    }
}
