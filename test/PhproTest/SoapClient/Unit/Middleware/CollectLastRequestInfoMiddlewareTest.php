<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\PluginClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Mock\Client;
use Phpro\SoapClient\Middleware\CollectLastRequestInfoMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Soap\Handler\LastRequestInfoCollectorInterface;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use PHPUnit\Framework\TestCase;

/**
 * Class CollectLastRequestInfoMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class CollectLastRequestInfoMiddlewareTest extends TestCase
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
     * @var CollectLastRequestInfoMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp(): void
    {
        $this->middleware = new CollectLastRequestInfoMiddleware();
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
    function it_is_a_last_request_info_collector_middleware()
    {
        $this->assertInstanceOf(LastRequestInfoCollectorInterface::class, $this->middleware);
    }

    /**
     * @test
     */
    function it_has_a_name()
    {
        $this->assertEquals('collect_last_request_info_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_remembers_the_last_request_and_response()
    {
        $this->mockClient->addResponse($response = new Response(200, ['Content-Type' => 'text/plain'], 'response'));
        $this->client->sendRequest($request = new Request('POST', '/', ['User-Agent' => 'no'], 'request'));

        $result = $this->middleware->collectLastRequestInfo();
        $this->assertInstanceOf(LastRequestInfo::class, $result);
        $this->assertEquals('request', $result->getLastRequest());
        $this->assertEquals('response', $result->getLastResponse());
        $this->assertEquals('User-Agent: no', trim($result->getLastRequestHeaders()));
        $this->assertEquals('Content-Type: text/plain', trim($result->getLastResponseHeaders()));
    }
}
