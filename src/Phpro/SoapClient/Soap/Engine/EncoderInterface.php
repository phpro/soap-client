<?php

namespace Phpro\SoapClient\Soap\Engine;

use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;

interface EncoderInterface
{
    public function encode(string $method, array $arguments): SoapRequest;
}
