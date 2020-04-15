<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\PluginClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Mock\Client;
use Phpro\SoapClient\Exception\RuntimeException;
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
     * @var PluginClient
     */
    private $client;

    /**
     * @var Client
     */
    private $mockClient;

    /**
     * @var NtlmMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp(): void
    {
        $this->middleware = new NtlmMiddleware('username', 'password');
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
        $this->assertEquals('ntlm_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_adds_ntlm_auth_to_the_request()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/CURLOPT_HTTPAUTH \= CURLAUTH_NTLM/i');
        $this->expectExceptionMessageMatches('/CURLOPT_USERPWD \= "username\:password"/i');

        $this->mockClient->addResponse(new Response());
        $this->client->sendRequest(new Request('POST', '/'));
    }
}
