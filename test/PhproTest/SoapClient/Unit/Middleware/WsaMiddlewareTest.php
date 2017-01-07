<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Middleware\WsaMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;

/**
 * Class WsaMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class WsaMiddlewareTest extends \PHPUnit_Framework_TestCase
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
     * @var WsaMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp()
    {
        $this->handler = new MockHandler([]);
        $this->middleware = new WsaMiddleware();
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
        $this->assertEquals('wsa_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_adds_wsa_to_the_request_xml()
    {
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request.xml');
        $expected = file_get_contents(FIXTURE_DIR . '/soap/wsa.xml');


        $this->handler->append($response = new Response(200));
        $result = $this->client->send($request = new Request(
            'POST',
            '/endpoint',
            ['SOAPAction' => 'myaction'],
            $soapRequest)
        );

        $actualRequestBody = (string)$this->handler->getLastRequest()->getBody();
        $found = preg_match(
            '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i',
            $actualRequestBody,
            $uuids
        );

        $this->assertEquals($result, $response);
        $this->assertEquals(1, $found, 'No WSA UUID could be found in the request.');
        $this->assertXmlStringEqualsXmlString(
            str_replace('{{UUID}}', $uuids[0], $expected),
            $actualRequestBody
        );
    }
}
