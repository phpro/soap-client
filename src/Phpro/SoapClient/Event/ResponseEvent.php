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
use Phpro\SoapClient\Type\ResultInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ResponseEvent
 *
 * @package Phpro\SoapClient\Event
 */
class ResponseEvent extends Event
{
    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * @var mixed
     */
    protected $response;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     * @param RequestEvent $requestEvent
     * @param ResultInterface $response
     */
    public function __construct(Client $client, RequestEvent $requestEvent, ResultInterface $response)
    {
        $this->client = $client;
        $this->requestEvent = $requestEvent;
        $this->response = $response;
    }

    /**
     * @return RequestEvent
     */
    public function getRequestEvent()
    {
        return $this->requestEvent;
    }

    /**
     * @return ResultInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
