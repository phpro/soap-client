<?php

namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Exception\SoapException;

/**
 * Class FaultEvent
 *
 * @package Phpro\SoapClient\Event
 */
class FaultEvent extends SoapEvent
{

    /**
     * @var SoapException
     */
    protected $soapException;

    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client        $client
     * @param SoapException $soapException
     * @param RequestEvent  $requestEvent
     */
    public function __construct(Client $client, SoapException $soapException, RequestEvent $requestEvent)
    {
        $this->client = $client;
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

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
