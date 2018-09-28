<?php

namespace Phpro\SoapClient\Soap\Engine;

use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;

interface EncoderInterface
{
    public function encode(string $name, array $arguments): SoapRequest;
}
