<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\ExtSoap;

use Phpro\SoapClient\Soap\Engine\Engine;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Soap\Engine\ExtSoap\Handler\ExtSoapClientHandle;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;

class ExtSoapEngineFactory
{

    public static function createFromOptions(ExtSoapOptions $options): EngineInterface
    {
        $client = AbusedClient::createFromOptions($options);
        $handler = new ExtSoapClientHandle($client);

        return self::createFromClientAndHandler($client, $handler);
    }

    public static function createFromClientAndHandler(AbusedClient $client, HandlerInterface $handler): EngineInterface
    {
        return new Engine(
            new ExtSoapMetadata($client),
            new ExtSoapEncoder($client),
            new ExtSoapDecoder($client),
            $handler
        );
    }
}
