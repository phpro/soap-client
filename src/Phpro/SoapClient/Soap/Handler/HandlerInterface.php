<?php

namespace Phpro\SoapClient\Soap\Handler;

use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

/**
 * Class HandlerInterface
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
interface HandlerInterface extends LastRequestInfoCollectorInterface
{
    /**
     * @param SoapRequest $request
     *
     * @return SoapResponse
     */
    public function request(SoapRequest $request): SoapResponse;
}
