<?php

namespace PhproTest\SoapClient\Integration\Soap;

use Phpro\SoapClient\Soap\SoapClient as PhproSoapClient;

/**
 * Class SoapClientTest
 *
 * @package PhproTest\SoapClient\Integration\Soap
 */
class SoapClientTest extends \PHPUnit_Framework_TestCase
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

        $this->assertTrue(array_key_exists('GetCityForecastByZIP', $types));
        $this->assertEquals('string', $types['GetCityForecastByZIP']['ZIP']);
    }

    /**
     * @test
     * @vcr soap-client-vcr-enabled.yml
     *
     * Note: this method will throw Exceptions if VCR can't take over the configured SoapClient.
     */
    function it_should_be_possible_to_hook_php_vcr_for_testing()
    {
        $result = $this->client->GetCityWeatherByZIP(['ZIP' => '10013']);
        $this->assertTrue($result->GetCityWeatherByZIPResult->Success);
    }
}
