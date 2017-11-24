<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Middleware\NtlmMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class NtlmMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class NtlmMiddlewareTest extends TestCase
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
     * @var NtlmMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp()
    {
        $this->handler = new MockHandler([]);
        $this->middleware = new NtlmMiddleware('username', 'password');
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
        $this->assertEquals('ntlm_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_adds_ntlm_auth_to_the_request()
    {
        $this->handler->append(new Response());
        $this->client->send(new Request('POST', '/'));
        $sentOptions = $this->handler->getLastOptions();
        $this->assertEquals(CURLAUTH_NTLM, $sentOptions['curl'][CURLOPT_HTTPAUTH]);
        $this->assertEquals('username:password', $sentOptions['curl'][CURLOPT_USERPWD]);
    }
}
