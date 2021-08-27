<?php

namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Caller;
use Phpro\SoapClient\Exception\SoapException;

class FaultEvent
{

    /**
     * @var SoapException
     */
    protected $soapException;

    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    public function __construct(SoapException $soapException, RequestEvent $requestEvent)
    {
        $this->soapException = $soapException;
        $this->requestEvent = $requestEvent;
    }

    /**
     * @return SoapException
     */
    public function getSoapException(): SoapException
    {
        return $this->soapException;
    }

    /**
     * @return RequestEvent
     */
    public function getRequestEvent(): RequestEvent
    {
        return $this->requestEvent;
    }
}
