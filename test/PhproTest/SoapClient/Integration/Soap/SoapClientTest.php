<?php

namespace PhproTest\SoapClient\Integration\Soap;

use Phpro\SoapClient\Soap\SoapClient as PhproSoapClient;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapClientTest
 *
 * @package PhproTest\SoapClient\Integration\Soap
 */
class SoapClientTest extends TestCase
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
        $this->client = new PhproSoapClient(self::CDYNE_WSDL, ['soap_version' => SOAP_1_2]);
    }

    /**
     * @test
     */
    function it_should_know_all_WSDL_types()
    {
        $types = $this->client->getSoapTypes();

        $type = null;
        foreach ($types as $type) {
            if ($type['typeName'] === 'GetCityForecastByZIP') {
                break;
            }
        }

        $this->assertNotNull($type);
        $this->assertEquals('string', $type['properties']['ZIP']);
    }

    /**
     * @test
     * @vcr soap-get-city-weather-by-zip-10013.yml
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
     * @vcr soap-get-city-weather-by-zip-10013.yml
     *
     *  Note: The headers are not remembered by the internally used php-vcr soapclient.
     */
    function it_should_know_the_last_request_and_response()
    {
        $this->assertEquals(0, strlen($this->client->__getLastRequest()));
        $this->assertEquals(0, strlen($this->client->__getLastResponse()));
        $this->client->GetCityWeatherByZIP(['ZIP' => '10013']);
        $this->assertGreaterThan(0, strlen($this->client->__getLastRequest()));
        $this->assertGreaterThan(0, strlen($this->client->__getLastResponse()));
    }
}
