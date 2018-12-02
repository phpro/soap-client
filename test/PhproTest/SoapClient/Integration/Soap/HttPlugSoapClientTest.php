<?php

namespace PhproTest\SoapClient\Integration\Soap;

use Http\Adapter\Guzzle6\Client;
use Phpro\SoapClient\Soap\Handler\HttPlugHandle;
use Phpro\SoapClient\Soap\SoapClient as PhproSoapClient;
use PHPUnit\Framework\TestCase;

/**
 * Class HttPlugSoapClientTest
 *
 * @package PhproTest\SoapClient\Integration\Soap
 */
class HttplugSoapClientTest extends TestCase
{
    /**
     * Wheather API
     */
    const CDYNE_WSDL = FIXTURE_DIR . '/wsdl/weather-ws.wsdl';

    /**
     * @var PhproSoapClient
     */
    protected $client;

    /**
     * Configure client
     */
    function setUp()
    {
        $this->markTestSkipped('TODO: refactoring to engine ...');

        $httpClient = Client::createWithConfig(['headers' => ['User-Agent' => 'testing/1.0']]);
        $this->client = new PhproSoapClient(self::CDYNE_WSDL, ['soap_version' => SOAP_1_2]);
        $this->client->setHandler(HttPlugHandle::createForClient($httpClient));
    }

    /**
     * @test
     * @vcr guzzle-get-city-weather-by-zip-10013.yml
     *
     * Note: this method will throw Exceptions if VCR can't take over the configured SoapClient.
     */
    function it_should_be_possible_to_hook_php_vcr_for_testing()
    {
        $result = $this->client->GetCityWeatherByZIP(['ZIP' => '10013']);
        $this->assertTrue($result->GetCityWeatherByZIPResult->Success);
    }

    /**
     * @test
     * @vcr guzzle-get-city-weather-by-zip-10013.yml
     */
    function it_should_know_the_last_request_and_response()
    {
        $this->assertEquals(0, strlen($this->client->__getLastRequest()));
        $this->assertEquals(0, strlen($this->client->__getLastResponse()));
        $this->assertEquals(0, strlen($this->client->__getLastRequestHeaders()));
        $this->assertEquals(0, strlen($this->client->__getLastResponseHeaders()));
        $this->client->GetCityWeatherByZIP(['ZIP' => '10013']);
        $this->assertGreaterThan(0, strlen($this->client->__getLastRequest()));
        $this->assertGreaterThan(0, strlen($this->client->__getLastResponse()));
        $this->assertGreaterThan(0, strlen($this->client->__getLastRequestHeaders()));
        $this->assertGreaterThan(0, strlen($this->client->__getLastResponseHeaders()));
    }
}
