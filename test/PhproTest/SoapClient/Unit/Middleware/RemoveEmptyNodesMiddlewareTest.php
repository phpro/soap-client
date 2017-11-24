<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Xml\SoapXml;
use Phpro\SoapClient\Middleware\Middleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Middleware\RemoveEmptyNodesMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Class RemoveEmptyNodesMiddlewareTest
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class RemoveEmptyNodesMiddlewareTest extends TestCase
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
        $this->middleware = new RemoveEmptyNodesMiddleware();
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
        $this->assertEquals('remove_empty_nodes_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_removes_empty_nodes_from_request_xml()
    {
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/with-empty-nodes-request.xml');
        $this->handler->append($response = new Response(200));
        $this->client->send($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        $this->assertEquals($xml->xpath('//env:Body/ns1:UpdateCustomers/*')->length, 3, 'Not all empty nodes are removed');
        $this->assertEquals($xml->xpath('//env:Body/ns1:UpdateCustomers/ns1:UserID')->length, 1, 'Not empty node is removed');
        $this->assertEquals($xml->xpath('//env:Body/ns1:UpdateCustomers/ns1:CustomerID')->length, 0, 'Empty node is removed');
        $this->assertEquals($xml->xpath('//env:Body/ns1:UpdateCustomers/ns1:Customer/ns1:MailAddress')->length, 1, 'Not empty parent node is removed');
        $this->assertEquals($xml->xpath('//env:Body/ns1:UpdateCustomers/ns1:Customer/ns1:MailAddress/*')->length, 3, 'Not all empty child nodes removed');
        $this->assertEquals($xml->xpath('//env:Body/ns1:UpdateCustomers/ns1:Customer/ns1:MailAddress/ns1:AddressID')->length, 0, 'Empty child node is removed');
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

        return new SoapXml($xml);
    }
}
