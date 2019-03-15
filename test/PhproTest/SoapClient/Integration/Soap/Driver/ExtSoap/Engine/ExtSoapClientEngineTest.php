<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Driver\ExtSoap\Engine;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapClientHandle;
use Phpro\SoapClient\Soap\Engine\Engine;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use PhproTest\SoapClient\Integration\Soap\Engine\AbstractEngineTest;

class ExtSoapClientEngineTest extends AbstractEngineTest
{
    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var HandlerInterface
     */
    private $handler;

    protected function getEngine(): EngineInterface
    {
        return $this->engine;
    }

    protected function getHandler(): HandlerInterface
    {
        return $this->handler;
    }

    protected function getVcrPrefix(): string
    {
        return 'ext-soap-with-client-handle-';
    }

    protected function skipVcr(): bool
    {
        return false;
    }

    protected function skipLastHeadersCheck(): bool
    {
        return true;
    }

    protected function configureForWsdl(string $wsdl)
    {
        $this->engine = new Engine(
            ExtSoapDriver::createFromClient(
                $client = AbusedClient::createFromOptions(
                    ExtSoapOptions::defaults($wsdl, [
                        'cache_wsdl' => WSDL_CACHE_NONE,
                        'soap_version' => SOAP_1_2,
                    ])
                )
            ),
            $this->handler = new ExtSoapClientHandle($client)
        );
    }
}
