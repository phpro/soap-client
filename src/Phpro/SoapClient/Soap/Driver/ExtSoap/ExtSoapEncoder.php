<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\Engine\EncoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;

class ExtSoapEncoder implements EncoderInterface
{
    /**
     * @var AbusedClient
     */
    private $client;

    public function __construct(AbusedClient $client)
    {
        $this->client = $client;
    }

    public function encode(string $method, array $arguments): SoapRequest
    {
        try {
            $this->client->__soapCall($method, $arguments);
            $encoded = $this->client->collectRequest();
        } finally {
            $this->client->cleanUpTemporaryState();
        }

        return $encoded;
    }
}
