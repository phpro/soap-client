<?php

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
