<?php

namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
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
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     * @param SoapFault $soapFault
     * @param RequestEvent $requestEvent
     */
    public function __construct(Client $client, SoapFault $soapFault, RequestEvent $requestEvent)
    {
        $this->client = $client;
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

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
