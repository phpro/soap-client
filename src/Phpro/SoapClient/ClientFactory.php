<?php

namespace Phpro\SoapClient;

use SoapClient;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @param SoapClient      $soapClient
     * @param EventDispatcherInterface $dispatcher
     *
     * @return ClientInterface
     */
    public function factory(SoapClient $soapClient, EventDispatcherInterface $dispatcher): ClientInterface
    {
        $rc = new ReflectionClass($this->className);
        $obj = $rc->newInstance($soapClient, $dispatcher);
    }
}
