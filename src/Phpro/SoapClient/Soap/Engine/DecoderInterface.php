<?php

namespace Phpro\SoapClient\Soap\Engine;

use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

interface DecoderInterface
{
    public function decode(SoapResponse $response);
}
