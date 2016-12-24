<?php

namespace Phpro\SoapClient\Soap\Handler;

use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use Phpro\SoapClient\Soap\SoapClient;

/**
 * Class SoapHandle
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
class SoapHandle implements HandlerInterface
{
    /**
     * @var SoapClient
     */
    private $client;

    /**
     * SoapHandle constructor.
     *
     * @param SoapClient $client
     */
    public function __construct(SoapClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param SoapRequest $request
     *
     * @return SoapResponse
     */
    public function createRequest(SoapRequest $request): SoapResponse
    {
        return new SoapResponse(
            $this->client->__doInternalRequest(
                $request->getRequest(),
                $request->getLocation(),
                $request->getAction(),
                $request->getVersion(),
                $request->getOneWay()
            )
        );
    }
}
