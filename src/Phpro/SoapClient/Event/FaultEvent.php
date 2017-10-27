<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Exception\SoapException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FaultEvent
 *
 * @package Phpro\SoapClient\Event
 */
class FaultEvent extends Event
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
    public function getSoapException()
    {
        return $this->soapException;
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
