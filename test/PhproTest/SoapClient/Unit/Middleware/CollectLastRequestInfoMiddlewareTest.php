<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Middleware\CollectLastRequestInfoMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Soap\Handler\LastRequestInfoCollectorInterface;

/**
 * Class CollectLastRequestInfoMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class CollectLastRequestInfoMiddlewareTest extends \PHPUnit_Framework_TestCase
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
     * @var CollectLastRequestInfoMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp()
    {
        $this->handler = new MockHandler([]);
        $this->middleware = new CollectLastRequestInfoMiddleware();
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
        $this->handler->append($response = new Response(200, ['Content-Type' => 'text/plain'], 'response'));
        $this->client->send($request = new Request('POST', '/', ['User-Agent' => 'no'], 'request'));

        $result = $this->middleware->collectLastRequestInfo();
        $this->assertEquals('request', $result->getLastRequest());
        $this->assertEquals('response', $result->getLastResponse());
        $this->assertEquals('User-Agent: no', trim($result->getLastRequestHeaders()));
        $this->assertEquals('Content-Type: text/plain', trim($result->getLastResponseHeaders()));
    }
}
