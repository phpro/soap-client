<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param SoapClient      $soapClient
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
