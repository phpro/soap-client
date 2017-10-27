<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Soap\Handler;

use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;

/**
 * Class LastRequestInfoCollectorInterface
 *
 * @package Phpro\SoapClient\Soap\HttpBinding
 */
interface LastRequestInfoCollectorInterface
{
    /***
     * @return LastRequestInfo
     */
    public function collectLastRequestInfo(): LastRequestInfo;
}
