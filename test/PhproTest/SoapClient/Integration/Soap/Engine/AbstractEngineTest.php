<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Xml\SoapXml;
use VCR\VCR;

abstract class AbstractEngineTest extends AbstractIntegrationTest
{
    abstract protected function getEngine(): EngineInterface;
    abstract protected function getHandler(): HandlerInterface;
    abstract protected function getVcrPrefix(): string;

    /**
     * Skips inserting a php-vcr cassette
     */
    abstract protected function skipVcr(): bool;

    /**
     * Some handlers don't return HTTP headers. This method can skip validation of the sent / received headers
     */
    abstract protected function skipLastHeadersCheck(): bool;

    /**
     * @test
     * @runInSeparateProcess
     * Note: this method will throw Exceptions if VCR can't take over the configured SoapClient.
     */
    function it_should_be_possible_to_hook_php_vcr_for_testing()
    {
        $this->runWithCasette('get-city-weather-by-zip-10013.yml', function() {
            $this->configureForWsdl(FIXTURE_DIR . '/wsdl/weather-ws.wsdl');
            $result = $this->getEngine()->request('GetCityWeatherByZIP', [['ZIP' => '10013']]);
            $this->assertTrue($result->GetCityWeatherByZIPResult->Success);
        });
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_should_know_the_last_request_and_response()
    {
        $this->runWithCasette('get-city-weather-by-zip-10013.yml', function() {
            $this->configureForWsdl(FIXTURE_DIR . '/wsdl/weather-ws.wsdl');
            $handler = $this->getHandler();
            $lastInfo = $handler->collectLastRequestInfo();
            $this->assertEquals(0, strlen($lastInfo->getLastRequest()));
            $this->assertEquals(0, strlen($lastInfo->getLastResponse()));
            if (!$this->skipLastHeadersCheck()) {
                $this->assertEquals(0, strlen($lastInfo->getLastRequestHeaders()));
                $this->assertEquals(0, strlen($lastInfo->getLastResponseHeaders()));
            }

            $result = $this->getEngine()->request('GetCityWeatherByZIP', [['ZIP' => '10013']]);

            $lastInfo = $handler->collectLastRequestInfo();
            $this->assertGreaterThan(0, strlen($lastInfo->getLastRequest()));
            $this->assertGreaterThan(0, strlen($lastInfo->getLastResponse()));


            if (!$this->skipLastHeadersCheck()) {
                $this->assertGreaterThan(0, strlen($lastInfo->getLastRequestHeaders()));
                $this->assertGreaterThan(0, strlen($lastInfo->getLastResponseHeaders()));
            }

            // Try parsing xml
            $request = SoapXml::fromString($lastInfo->getLastRequest());
            $this->assertEquals($request->getEnvelope(), $request->getRootElement());
            $response = SoapXml::fromString($lastInfo->getLastRequest());
            $this->assertEquals($request->getEnvelope(), $response->getRootElement());
        });
    }

    private function runWithCasette(string $cassete, callable $test)
    {
        if ($this->skipVcr()) {
            $test();
            return;
        }

        try {
            VCR::insertCassette($this->getVcrPrefix().$cassete);
            $test();
        } finally {
            Vcr::eject();
        }
    }
}
