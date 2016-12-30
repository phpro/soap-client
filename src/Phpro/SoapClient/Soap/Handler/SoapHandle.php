<?php

namespace Phpro\SoapClient\Soap\Handler;

use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use Phpro\SoapClient\Soap\SoapClient;

/**
 * Class SoapHandle
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
class SoapHandle implements HandlerInterface, LastRequestInfoCollectorInterface
{
    /**
     * @var SoapClient
     */
    private $client;

    /**
     * @var LastRequestInfo
     */
    private $lastRequestInfo;

    /**
     * SoapHandle constructor.
     *
     * @param SoapClient $client
     */
    public function __construct(SoapClient $client)
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
        $response = $this->client->doInternalRequest(
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
