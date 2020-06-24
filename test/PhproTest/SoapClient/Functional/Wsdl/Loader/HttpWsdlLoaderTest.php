<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Functional\Wsdl;

use Phpro\SoapClient\Wsdl\Loader\HttpWsdlLoader;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\RequestMatcher\RequestMatcher;
use Http\Mock\Client;
use Phpro\SoapClient\Wsdl\Loader\WsdlLoaderInterface;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;
use PHPUnit\Framework\TestCase;

class HttpWsdlLoaderTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var HttpWsdlLoader
     */
    private $loader;

    protected function setUp(): void
    {
        $this->loader = new HttpWsdlLoader(
            $this->client = new Client(),
            Psr17FactoryDiscovery::findRequestFactory()
        );
    }

    /** @test */
    public function it_is_a_wsdl_loader(): void
    {
        self::assertInstanceOf(WsdlLoaderInterface::class, $this->loader);
    }

    /** @test */
    public function it_can_load_wsdl(): void
    {
        $url = Psr17FactoryDiscovery::findUrlFactory()->createUri('http://localhost/some/service?wsdl');
        $matcher = new RequestMatcher($url->getPath(), $url->getHost(), ['GET'], ['http']);
        $response = Psr17FactoryDiscovery::findResponseFactory()->createResponse()->withBody(
            Psr17FactoryDiscovery::findStreamFactory()->createStream($body = 'wsdl body')
        );

        $this->client->on($matcher, $response);

        self::assertSame($body, $this->loader->load((string) $url));
    }
}
