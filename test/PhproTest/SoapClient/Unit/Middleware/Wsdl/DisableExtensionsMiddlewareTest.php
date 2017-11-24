<?php

namespace PhproTest\SoapClient\Unit\Middleware\Wsdl;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
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
     * @var ClientInterface
     */
    private $client;

    /**
     * @var MockHandler
     */
    private $handler;

    /**
     * @var DisableExtensionsMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp()
    {
        $this->handler = new MockHandler([]);
        $this->middleware = new DisableExtensionsMiddleware();
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
        $this->assertEquals('wsdl_disable_extensions', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_removes_required_wsdl_extensions()
    {
        $this->handler->append(new Response(
            200,
            [],
            file_get_contents(FIXTURE_DIR . '/wsdl/wsdl-extensions.wsdl'))
        );

        $response = $this->client->send(new Request('POST', '/'));
        $xml = WsdlXml::fromStream($response->getBody());
        $xpath = '//wsdl:binding/wsaw:UsingAddressing[@wsdl:required="%s"]';

        $this->assertEquals(0, $xml->xpath(sprintf($xpath, 'true'))->length, 'Still got required WSDL extensions.');
        $this->assertEquals(1, $xml->xpath(sprintf($xpath, 'false'))->length, 'Cannot find any deactivated WSDL extensions.');
    }
}
