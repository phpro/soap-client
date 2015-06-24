<?php

namespace Phpro\SoapClient\Event;

use SoapFault;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FaultEvent
 *
 * @package Phpro\SoapClient\Event
 */
class FaultEvent extends Event
{

    /**
     * @var SoapFault
     */
    protected $soapFault;

    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * @param SoapFault    $soapFault
     * @param RequestEvent $requestEvent
     */
    public function __construct(SoapFault $soapFault, RequestEvent $requestEvent)
    {
        $this->soapFault = $soapFault;
        $this->requestEvent = $requestEvent;
    }

    /**
     * @return SoapFault
     */
    public function getSoapFault()
    {
        return $this->soapFault;
    }

    /**
     * @return RequestEvent
     */
    public function getRequestEvent()
    {
        return $this->requestEvent;
    }
}