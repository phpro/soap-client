<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\ExtSoap;

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

    public function encode(string $name, array $arguments): SoapRequest
    {
        $this->client->__soapCall($name, $arguments);

        return $this->client->collectRequest();
    }
}
