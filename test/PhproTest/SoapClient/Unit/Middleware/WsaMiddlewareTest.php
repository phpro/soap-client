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
use Phpro\SoapClient\Xml\SoapXml;
use PHPUnit\Framework\TestCase;

/**
 * Class WsaMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class WsaMiddlewareTest extends TestCase
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
        $this->handler->append($response = new Response(200));
        $result = $this->client->send($request = new Request(
            'POST',
            '/endpoint',
            ['SOAPAction' => 'myaction'],
            $soapRequest)
        );

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        // Make sure the response is available:
        $this->assertEquals($response, $result);

        // Check structure
        $this->assertEquals(1, $xml->xpath('//soap:Header/wsa:Action')->length, 'No WSA Action tag');
        $this->assertEquals(1, $xml->xpath('//soap:Header/wsa:To')->length, 'No WSA To tag');
        $this->assertEquals(1, $xml->xpath('//soap:Header/wsa:MessageID')->length, 'No WSA MessageID tag');
        $this->assertEquals(1, $xml->xpath('//soap:Header/wsa:ReplyTo')->length, 'No WSA ReplyTo tag');
        $this->assertEquals(1, $xml->xpath('//soap:Header/wsa:ReplyTo/wsa:Address')->length, 'No WSA ReplyTo Address tag');

        // Check defaults:
        $this->assertEquals('myaction', $xml->xpath('//soap:Header/wsa:Action')->item(0)->nodeValue);
        $this->assertEquals('/endpoint', $xml->xpath('//soap:Header/wsa:To')->item(0)->nodeValue);
        $this->assertRegExp(
            '/^uuid:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $xml->xpath('//soap:Header/wsa:MessageID')->item(0)->nodeValue
        );
        $this->assertEquals(
            WsaMiddleware::WSA_ADDRESS_ANONYMOUS,
            $xml->xpath('//soap:Header/wsa:ReplyTo/wsa:Address')->item(0)->nodeValue
        );
    }

    /**
     * @param $soapBody
     *
     * @return SoapXml
     */
    private function fetchSoapXml($soapBody): SoapXml
    {
        $xml = new \DOMDocument();
        $xml->loadXML($soapBody);

        $soapXml = new SoapXml($xml);
        $soapXml->registerNamespace('wsa', 'http://schemas.xmlsoap.org/ws/2004/08/addressing');

        return $soapXml;
    }
}
