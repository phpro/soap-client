<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Soap\SoapClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ClientFactoryInterface
 *
 * @package Phpro\SoapClient
 */
interface ClientFactoryInterface
{

    /**
     * @param SoapClient      $soapClient
     * @param EventDispatcherInterface $dispatcher
     *
     * @return ClientInterface
     */
    public function factory(SoapClient $soapClient, EventDispatcherInterface $dispatcher);

}
