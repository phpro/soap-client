<?php

namespace Phpro\SoapClient;

use ReflectionClass;
use SoapClient;
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
     * @param SoapClient               $soapClient
     * @param EventDispatcherInterface $dispatcher
     *
     * @return object
     */
    public function factory(SoapClient $soapClient, EventDispatcherInterface $dispatcher)
    {
        $rc = new ReflectionClass($this->className);

        return $rc->newInstance($soapClient, $dispatcher);
    }
}
