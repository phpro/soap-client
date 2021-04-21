<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\PluginClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Mock\Client;
use Phpro\SoapClient\Middleware\WsaMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Xml\SoapXml;
use PHPUnit\Framework\TestCase;
use RobRichards\WsePhp\WSASoap;

/**
 * Class WsaMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class WsaMiddlewareTest extends TestCase
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
     * @var WsaMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp(): void
    {
        $this->middleware = new WsaMiddleware();
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
        $this->assertEquals('wsa_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_adds_wsa_to_the_request_xml()
    {
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request.xml');
        $this->mockClient->addResponse($response = new Response(200));
        $result = $this->client->sendRequest($request = new Request(
            'POST',
            '/endpoint',
            ['SOAPAction' => 'myaction'],
            $soapRequest)
        );

        $soapBody = (string)$this->mockClient->getRequests()[0]->getBody();
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
        $this->assertMatchesRegularExpression(
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
        $soapXml->registerNamespace('wsa', WSASoap::WSANS);

        return $soapXml;
    }
}
