<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Driver\ExtSoap\Engine;

use GuzzleHttp\Client;
use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Engine\Engine;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\Handler\HttPlugHandle;
use PhproTest\SoapClient\Integration\Soap\Engine\AbstractEngineTest;

class ExtSoapHttPlugEngineTest extends AbstractEngineTest
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
        return 'ext-soap-with-httplug-handle-';
    }

    protected function skipVcr(): bool
    {
        return false;
    }

    protected function skipLastHeadersCheck(): bool
    {
        return false;
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
            $this->handler = HttPlugHandle::createForClient(
                new Client(['headers' => ['User-Agent' => 'testing/1.0']])
            )
        );
    }
}
