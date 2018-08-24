<?php

namespace PhproTest\SoapClient\Integration\Soap;

use Phpro\SoapClient\Soap\Handler\LocalSoapServerHandle;
use Phpro\SoapClient\Soap\SoapClient as PhproSoapClient;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapClientTest
 *
 * @package PhproTest\SoapClient\Integration\Soap
 */
class LocalSoapServerClientTest extends TestCase
{

    /**
     * Wheather API
     */
    const CDYNE_WSDL = FIXTURE_DIR . '/wsdl/wheater-ws.wsdl';

    /**
     * @var \SoapServer
     */
    private $server;

    /**
     * @var PhproSoapClient
     */
    private $client;

    /**
     * Configure client
     */
    protected function setUp()
    {
        $this->server = new \SoapServer(self::CDYNE_WSDL, ['soap_version' => SOAP_1_2]);
        $this->client = new PhproSoapClient(self::CDYNE_WSDL, ['soap_version' => SOAP_1_2]);
        $this->client->setHandler(new LocalSoapServerHandle($this->server));

        $this->server->setObject(new class() {
            public function GetCityWeatherByZIP($zip) {
                return [
                    'GetCityWeatherByZIPResult' => [
                        'WeatherID' => 1,
                        'Success' => true,
                    ]
                ];
            }
        });
    }

    /**
     * @test
     * @@runInSeparateProcess
     */
    function it_should_run_through_soap_server()
    {
        $result = $this->client->GetCityWeatherByZIP(['ZIP' => '10013']);
        $this->assertTrue($result->GetCityWeatherByZIPResult->Success);
    }

    /**
     * @test
     * @@runInSeparateProcess
     */
    function it_should_know_the_last_request_and_response()
    {
        $this->assertEquals(0, strlen($this->client->__getLastRequest()));
        $this->assertEquals(0, strlen($this->client->__getLastResponse()));
        $this->client->GetCityWeatherByZIP(['ZIP' => '10013']);
        $this->assertGreaterThan(0, strlen($this->client->__getLastRequest()));
        $this->assertGreaterThan(0, strlen($this->client->__getLastRequest()));
    }
}
