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

/**
 * Class Events
 *
 * @package Phpro\SoapClient
 */
final class Events
{
    const REQUEST    = 'phpro.soap_client.request';
    const RESPONSE   = 'phpro.soap_client.response';
    const FAULT      = 'phpro.soap_client.fault';
}
