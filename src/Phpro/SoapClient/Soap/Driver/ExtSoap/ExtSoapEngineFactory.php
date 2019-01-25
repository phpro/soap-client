<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapClientHandle;
use Phpro\SoapClient\Soap\Engine\Engine;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;

class ExtSoapEngineFactory
{
    public static function fromOptions(ExtSoapOptions $options): Engine
    {
        $driver = ExtSoapDriver::createFromOptions($options);
        $handler = new ExtSoapClientHandle($driver->getClient());

        return new Engine($driver, $handler);
    }

    public static function fromOptionsWithHandler(ExtSoapOptions $options, HandlerInterface $handler): Engine
    {
        $driver = ExtSoapDriver::createFromOptions($options);

        return new Engine($driver, $handler);
    }
}
