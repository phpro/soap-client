<?php

namespace PhproTest\SoapClient\Unit\Middleware\Wsdl;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\PluginClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Mock\Client;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Middleware\Wsdl\DisableExtensionsMiddleware;
use Phpro\SoapClient\Xml\WsdlXml;
use PHPUnit\Framework\TestCase;

/**
 * Class BasicAuthMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class DisableExtensionsMiddlewareTest extends TestCase
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
     * @var DisableExtensionsMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp(): void
    {
        $this->middleware = new DisableExtensionsMiddleware();
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
        $this->assertEquals('wsdl_disable_extensions', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_removes_required_wsdl_extensions()
    {
        $this->mockClient->addResponse(new Response(
            200,
            [],
            file_get_contents(FIXTURE_DIR . '/wsdl/wsdl-extensions.wsdl'))
        );

        $response = $this->client->sendRequest(new Request('POST', '/'));
        $xml = WsdlXml::fromStream($response->getBody());
        $xpath = '//wsdl:binding/wsaw:UsingAddressing[@wsdl:required="%s"]';

        $this->assertEquals(0, $xml->xpath(sprintf($xpath, 'true'))->length, 'Still got required WSDL extensions.');
        $this->assertEquals(1, $xml->xpath(sprintf($xpath, 'false'))->length, 'Cannot find any deactivated WSDL extensions.');
    }
}
