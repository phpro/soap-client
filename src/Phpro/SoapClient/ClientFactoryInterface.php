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
