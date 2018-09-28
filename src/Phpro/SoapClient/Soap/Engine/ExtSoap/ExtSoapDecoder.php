<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\ExtSoap;

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

    public function decode(SoapResponse $response)
    {
        $this->client->registerResponse($response->getResponse());
        return $this->client->__doRequest('pseudo', 'location', 'action', SOAP_1_2);
    }
}
