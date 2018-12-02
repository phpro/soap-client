<?php

namespace Phpro\SoapClient\Soap\Engine;

use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

interface DecoderInterface
{
    /**
     * @return mixed
     */
    public function decode(string $method, SoapResponse $response);
}
