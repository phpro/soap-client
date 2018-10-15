<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\Engine\DecoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

class ExtSoapDecoder implements DecoderInterface
{
    /**
     * @var AbusedClient
     */
    private $client;

    public function __construct(AbusedClient $client)
    {
        $this->client = $client;
    }

    public function decode(string $method, SoapResponse $response)
    {
        $this->client->registerResponse($response);
        $decoded =  $this->client->__soapCall($method, []);
        $this->client->cleanUpTemporaryState();

        return $decoded;
    }
}
