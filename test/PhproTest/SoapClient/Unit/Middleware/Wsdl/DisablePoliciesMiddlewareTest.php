<?php

namespace PhproTest\SoapClient\Unit\Middleware\Wsdl;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\PluginClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Mock\Client;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Middleware\Wsdl\DisablePoliciesMiddleware;
use Phpro\SoapClient\Xml\WsdlXml;
use PHPUnit\Framework\TestCase;

/**
 * Class BasicAuthMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class DisablePoliciesMiddlewareTest extends TestCase
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
     * @var DisablePoliciesMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp(): void
    {
        $this->middleware = new DisablePoliciesMiddleware();
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
        $this->assertEquals('wsdl_disable_policies', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_removes_wsdl_policies()
    {
        $this->mockClient->addResponse(new Response(
            200,
            [],
            file_get_contents(FIXTURE_DIR . '/wsdl/wsdl-policies.wsdl'))
        );

        $response = $this->client->sendRequest(new Request('POST', '/'));
        $xml = WsdlXml::fromStream($response->getBody());

        $this->assertEquals(0, $xml->xpath('//wsdl:UsingPolicy')->length, 'Still got imports of WSDL policies (<UsingPolicy>).');
        $this->assertEquals(0, $xml->xpath('//wsdl:Polocy')->length, 'Still got references to WSDL policies (<Policy>).');
    }
}
