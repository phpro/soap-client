<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Wsdl\Loader;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class HttpWsdlLoader implements WsdlLoaderInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    public function load(string $wsdl): string
    {
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest('GET', $wsdl)
        );

        return (string) $response->getBody();
    }
}
