<?php

namespace Phpro\SoapClient;

use Phpro\SoapClient\Soap\SoapClient;
use Symfony\Component\EventDispatcher\EventDispatcher;
use ReflectionClass;

/**
 * Class ClientFactory
 *
 * @package Phpro\SoapClient
 */
class ClientFactory implements ClientFactoryInterface
{

    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param SoapClient      $soapClient
     * @param EventDispatcher $dispatcher
     *
     * @return object
     */
    public function factory(SoapClient $soapClient, EventDispatcher $dispatcher)
    {
        $rc = new ReflectionClass($this->className);
        return $rc->newInstance($soapClient, $dispatcher);
    }
}
