<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapServerHandle;
use Phpro\SoapClient\Soap\Engine\Engine;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use Phpro\SoapClient\Xml\SoapXml;
use PHPUnit\Framework\TestCase;

class ExtSoapEngineTest extends TestCase
{
    /**
     * Weather API
     */
    const CDYNE_WSDL = FIXTURE_DIR . '/wsdl/weather-ws.wsdl';

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var ExtSoapServerHandle
     */
    private $handler;

    /**
     * @var ExtSoapDriver
     */
    private $driver;

    /**
     * @var AbusedClient
     */
    private $client;

    /**
     * Configure client
     */
    protected function setUp()
    {
        $options = new ExtSoapOptions(self::CDYNE_WSDL, ['cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2]);
        $server = new \SoapServer($options->getWsdl(), $options->getOptions());
        $this->handler = new ExtSoapServerHandle($server);
        $this->client = AbusedClient::createFromOptions($options);
        $this->driver = ExtSoapDriver::createFromClient($this->client);
        $this->engine = new Engine($this->driver, $this->handler);

        $server->setObject(new class() {
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
     */
    function it_should_be_able_to_encode_request()
    {
        $encoded = $this->driver->encode('GetCityWeatherByZIP', [(object)['ZIP' => '10013']]);

        $this->assertInstanceOf(SoapRequest::class, $encoded);
        $this->assertEquals('http://wsf.cdyne.com/WeatherWS/Weather.asmx', $encoded->getLocation());
        $this->assertEquals('http://ws.cdyne.com/WeatherWS/GetCityWeatherByZIP', $encoded->getAction());
        $this->assertEquals(SOAP_1_2, $encoded->getVersion());
        $this->assertEquals(0, $encoded->getOneWay());

        /** @var SoapXml $xml */
        $xml = SoapXml::fromString($encoded->getRequest());
        $xml->registerNamespace('weather', 'http://ws.cdyne.com/WeatherWS/');
        $body = $xml->getBody();
        $actionTagXpath = $xml->xpath('./weather:GetCityWeatherByZIP', $body);
        $zipTagXpath = $xml->xpath('./weather:GetCityWeatherByZIP/weather:ZIP', $body);

        $this->assertEquals(1, $actionTagXpath->count(), 'Action tag not found');
        $this->assertEquals(1, $zipTagXpath->count(), 'ZIP parameter not found');
        $this->assertEquals('10013', $zipTagXpath->item(0)->nodeValue, 'ZIP does not match');
    }

    /**
     * @test
     */
    function it_should_be_able_to_decode_response()
    {
        $result = <<< EOXML
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:ns1="http://ws.cdyne.com/WeatherWS/">
    <env:Body>
        <ns1:GetCityWeatherByZIPResponse>
            <ns1:GetCityWeatherByZIPResult>
                <ns1:Success>true</ns1:Success>
                <ns1:WeatherID>1</ns1:WeatherID>
            </ns1:GetCityWeatherByZIPResult>
        </ns1:GetCityWeatherByZIPResponse>
    </env:Body>
</env:Envelope>
EOXML;

        $decoded = $this->driver->decode('GetCityWeatherByZIP', new SoapResponse($result));
        $this->assertTrue($decoded->GetCityWeatherByZIPResult->Success);
        $this->assertEquals(1, $decoded->GetCityWeatherByZIPResult->WeatherID);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_should_run_through_soap_server()
    {
        $result = $this->engine->request('GetCityWeatherByZIP', [(object)['ZIP' => '10013']]);
        $this->assertTrue($result->GetCityWeatherByZIPResult->Success);
        $this->assertEquals(1, $result->GetCityWeatherByZIPResult->WeatherID);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_should_know_the_last_request_and_response()
    {
        $this->assertEquals(0, strlen($this->handler->collectLastRequestInfo()->getLastRequest()));
        $this->assertEquals(0, strlen($this->handler->collectLastRequestInfo()->getLastResponse()));
        $this->engine->request('GetCityWeatherByZIP', [(object)['ZIP' => '10013']]);
        $this->assertGreaterThan(0, strlen($this->handler->collectLastRequestInfo()->getLastRequest()));
        $this->assertGreaterThan(0, strlen($this->handler->collectLastRequestInfo()->getLastResponse()));
    }

    /**
     * @test
     */
    function it_can_parse_metadata()
    {

    }
}
