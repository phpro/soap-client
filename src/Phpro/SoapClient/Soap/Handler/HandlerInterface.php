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

use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

/**
 * Class HandlerInterface
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
interface HandlerInterface
{

    /**
     * @param SoapRequest $request
     *
     * @return SoapResponse
     */
    public function request(SoapRequest $request): SoapResponse;
}
