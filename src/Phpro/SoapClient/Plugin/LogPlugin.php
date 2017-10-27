<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Plugin;

use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Event\ResponseEvent;
use Phpro\SoapClient\Event\FaultEvent;
use Phpro\SoapClient\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogPlugin
 *
 * @package Phpro\SoapClient\Plugin
 */
class LogPlugin implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param RequestEvent $event
     */
    public function onClientRequest(RequestEvent $event)
    {
        $this->logger->info(sprintf(
            '[phpro/soap-client] request: call "%s" with params %s',
            $event->getMethod(),
            print_r($event->getRequest(), true)
        ));
    }

    /**
     * @param ResponseEvent $event
     */
    public function onClientResponse(ResponseEvent $event)
    {
        $this->logger->info(sprintf(
            '[phpro/soap-client] response: %s',
            print_r($event->getResponse(), true)
        ));
    }

    /**
     * @param FaultEvent $event
     */
    public function onClientFault(FaultEvent $event)
    {
        $this->logger->error(sprintf(
            '[phpro/soap-client] fault "%s" for request "%s" with params %s',
            $event->getSoapException()->getMessage(),
            $event->getRequestEvent()->getMethod(),
            print_r($event->getRequestEvent()->getRequest(), true)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::REQUEST  => 'onClientRequest',
            Events::RESPONSE => 'onClientResponse',
            Events::FAULT    => 'onClientFault'
        );
    }
}
