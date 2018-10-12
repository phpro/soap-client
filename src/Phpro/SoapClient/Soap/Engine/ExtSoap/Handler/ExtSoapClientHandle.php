<?php

namespace Phpro\SoapClient\Soap\Engine\ExtSoap\Handler;

use Phpro\SoapClient\Soap\Engine\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\Handler\LastRequestInfoCollectorInterface;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

/**
 * Class SoapHandle
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
class ExtSoapClientHandle implements HandlerInterface, LastRequestInfoCollectorInterface
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

    /**
     * @param SoapRequest $request
     *
     * @return SoapResponse
     */
    public function request(SoapRequest $request): SoapResponse
    {
        $response = $this->client->doActualRequest(
            $request->getRequest(),
            $request->getLocation(),
            $request->getAction(),
            $request->getVersion(),
            $request->getOneWay()
        );

        $this->lastRequestInfo = LastRequestInfo::createFromSoapClient($this->client);

        return new SoapResponse($response);
    }

    /**
     * @return LastRequestInfo
     */
    public function collectLastRequestInfo(): LastRequestInfo
    {
        return $this->lastRequestInfo;
    }
}
