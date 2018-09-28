<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\ExtSoap;

use Phpro\SoapClient\Soap\Engine\Engine;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;

class ExtSoapEngineFactory
{
    public function create(ExtSoapOptions $options, HandlerInterface $handler): EngineInterface
    {
        $client = AbusedClient::createFromOptions($options);

        return new Engine(
            new ExtSoapMetadata($client),
            new ExtSoapEncoder($client),
            new ExtSoapDecoder($client),
            $handler
        );
    }
}
