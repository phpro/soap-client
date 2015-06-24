<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Soap\SoapClient;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class ClientFactoryInterface
 *
 * @package Phpro\SoapClient
 */
interface ClientFactoryInterface
{

    /**
     * @param SoapClient      $soapClient
     * @param EventDispatcher $dispatcher
     *
     * @return object
     */
    public function factory(SoapClient $soapClient, EventDispatcher $dispatcher);

}
