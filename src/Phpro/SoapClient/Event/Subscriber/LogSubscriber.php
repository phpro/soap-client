<?php

namespace Phpro\SoapClient\Event\Subscriber;

use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Event\ResponseEvent;
use Phpro\SoapClient\Event\FaultEvent;
use Phpro\SoapClient\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogSubscriber implements EventSubscriberInterface
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
    public static function getSubscribedEvents(): array
    {
        return array(
            Events::REQUEST  => 'onClientRequest',
            Events::RESPONSE => 'onClientResponse',
            Events::FAULT    => 'onClientFault'
        );
    }
}
