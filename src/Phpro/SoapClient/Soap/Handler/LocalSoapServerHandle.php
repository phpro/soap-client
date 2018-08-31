<?php

namespace Phpro\SoapClient\Soap\Handler;

use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use SoapServer;

class LocalSoapServerHandle implements HandlerInterface, LastRequestInfoCollectorInterface
{
    /**
     * @var SoapServer
     */
    private $server;

    /**
     * @var LastRequestInfo
     */
    private $lastRequestInfo;

    public function __construct(SoapServer $server)
    {
        $this->server = $server;
        $this->lastRequestInfo = LastRequestInfo::createEmpty();
    }

    /**
     * @param SoapRequest $request
     *
     * @return SoapResponse
     */
    public function request(SoapRequest $request): SoapResponse
    {
        ob_start();
        $this->server->handle($request->getRequest());
        $responseBody = ob_get_contents();
        ob_end_clean();

        $this->lastRequestInfo = new LastRequestInfo(
            '',
            $request->getRequest(),
            '',
            $responseBody
        );

        return new SoapResponse($responseBody);
    }

    /**
     * @return LastRequestInfo
     */
    public function collectLastRequestInfo(): LastRequestInfo
    {
        return $this->lastRequestInfo;
    }
}
