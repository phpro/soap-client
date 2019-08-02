<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\Driver\ExtSoap\Generator\DummyMethodArgumentsGenerator;
use Phpro\SoapClient\Soap\Engine\DecoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

class ExtSoapDecoder implements DecoderInterface
{
    /**
     * @var AbusedClient
     */
    private $client;

    /**
     * @var DummyMethodArgumentsGenerator
     */
    private $argumentsGenerator;

    public function __construct(AbusedClient $client, DummyMethodArgumentsGenerator $argumentsGenerator)
    {
        $this->client = $client;
        $this->argumentsGenerator = $argumentsGenerator;
    }

    public function decode(string $method, SoapResponse $response)
    {
        $this->client->registerResponse($response);
        try {
            $decoded = $this->client->__soapCall($method, $this->argumentsGenerator->generateForSoapCall($method));
        } finally {
            $this->client->cleanUpTemporaryState();
        }
        return $decoded;
    }
}
