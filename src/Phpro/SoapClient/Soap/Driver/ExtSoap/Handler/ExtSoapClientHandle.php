<?php

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Handler;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

class ExtSoapClientHandle implements HandlerInterface
{
    /**
     * @var AbusedClient
     */
    private $client;

    /**
     * @var LastRequestInfo
     */
    private $lastRequestInfo;

    public function __construct(AbusedClient $client)
    {
        $this->client = $client;
        $this->lastRequestInfo = LastRequestInfo::createEmpty();
    }

    public function request(SoapRequest $request): SoapResponse
    {
        $response = $this->client->doActualRequest(
            $request->getRequest(),
            $request->getLocation(),
            $request->getAction(),
            $request->getVersion(),
            $request->getOneWay()
        );

        $this->lastRequestInfo = new LastRequestInfo(
            (string) $this->client->__getLastRequestHeaders(),
            (string) $this->client->__getLastRequest(),
            (string) $this->client->__getLastResponseHeaders(),
            (string) $this->client->__getLastResponse()
        );

        return new SoapResponse($response);
    }

    public function collectLastRequestInfo(): LastRequestInfo
    {
        return $this->lastRequestInfo;
    }
}
