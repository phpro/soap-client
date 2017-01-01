<?php

namespace PhproTest\SoapClient\Integration\Soap;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Phpro\SoapClient\Soap\Handler\GuzzleHandle;
use Phpro\SoapClient\Soap\SoapClient as PhproSoapClient;

/**
 * Class GuzzleSoapClientTest
 *
 * @package PhproTest\SoapClient\Integration\Soap
 */
class GuzzleSoapClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Wheather API
     */
    const CDYNE_WSDL = FIXTURE_DIR . '/wsdl/wheater-ws.wsdl';


    /**
     * @var PhproSoapClient
     */
    protected $client;

    /**
     * @var HandlerStack
     */
    protected $handlerStack;

    /**
     * Configure client
     */
    function setUp()
    {
        $this->client = new PhproSoapClient(self::CDYNE_WSDL, ['soap_version' => SOAP_1_2]);
        $this->client->setHandler(GuzzleHandle::createForClient($guzzleClient = new Client()));
        $this->handlerStack = $guzzleClient->getConfig('handler');
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
