<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional\ExtSoap\Encoding;

use PhproTest\SoapClient\Functional\ExtSoap\AbstractSoapTestCase;
use Soap\Engine\SimpleEngine;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\Transport\TraceableTransport;

class EnumTest extends AbstractSoapTestCase
{
    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var ExtSoapDriver
     */
    private $driver;

    /**
     * @var TraceableTransport
     */
    private $transport;

    protected function setUp(): void
    {
        $this->wsdl = FIXTURE_DIR . '/wsdl/functional/enum.wsdl';
        $this->driver = $this->configureSoapDriver($this->wsdl, []);
        $this->transport = $this->configureServer(
            $this->wsdl,
            [],
            new class()
            {
                public function validate($input)
                {
                    return $input;
                }
            }
        );
    }

    /** @test */
    function it_does_not_register_a_type()
    {
        $types = $this->driver->getMetadata()->getTypes();
        $this->assertCount(0, $types);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_knows_how_to_add_enums()
    {
        $input = 'Home';
        $engine = new SimpleEngine($this->driver, $this->transport);
        $response = (string) $engine->request('validate', [$input]);
        $lastRequestInfo = $this->transport->collectLastRequestInfo();

        $this->assertEquals($input, $response);
        $this->assertStringContainsString(
            '<input xsi:type="ns2:PhoneTypeEnum">Home</input>',
            $lastRequestInfo->getLastRequest()
        );
        $this->assertStringContainsString(
            '<output xsi:type="ns2:PhoneTypeEnum">Home</output>',
            $lastRequestInfo->getLastResponse()
        );
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_does_not_validate_enums()
    {
        $input = 'INVALID';
        $engine = new SimpleEngine($this->driver, $this->transport);
        $engine->request('validate', [$input]);
        $lastRequestInfo = $this->transport->collectLastRequestInfo();

        $this->assertStringContainsString(
            '<input xsi:type="ns2:PhoneTypeEnum">INVALID</input>',
            $lastRequestInfo->getLastRequest()
        );
        $this->assertStringContainsString(
            '<output xsi:type="ns2:PhoneTypeEnum">INVALID</output>',
            $lastRequestInfo->getLastResponse()
        );
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_does_not_validate_enum_types()
    {
        $input = 123;
        $engine = new SimpleEngine($this->driver, $this->transport);
        $engine->request('validate', [$input]);
        $lastRequestInfo = $this->transport->collectLastRequestInfo();

        $this->assertStringContainsString(
            '<input xsi:type="ns2:PhoneTypeEnum">123</input>',
            $lastRequestInfo->getLastRequest()
        );
        $this->assertStringContainsString(
            '<output xsi:type="ns2:PhoneTypeEnum">123</output>',
            $lastRequestInfo->getLastResponse()
        );
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_can_be_transformed_with_type_map()
    {
        $this->driver = $this->configureSoapDriver($this->wsdl, [
            'typemap' => [
                [
                    'type_name' => 'PhoneTypeEnum',
                    'type_ns' => 'http://soapinterop.org/xsd',
                    'from_xml' => function($xml) {
                        $doc = new \DOMDocument();
                        $doc->loadXML($xml);

                        if ('' === $doc->textContent) {
                            return null;
                        }

                        return OfficeEnum::from($doc->textContent)->value;
                    },
                    'to_xml' => function($enum) {
                        return sprintf(
                            '<PhoneTypeEnum>%s</PhoneTypeEnum>',
                            $enum ? OfficeEnum::from($enum)->value : ''
                        );
                    },
                ]
            ]
        ]);
        $engine = new SimpleEngine($this->driver, $this->transport);

        $input = OfficeEnum::HOME->value;
        $response = $engine->request('validate', [$input]);
        $lastRequestInfo = $this->transport->collectLastRequestInfo();

        $this->assertEquals($input, $response);
        $this->assertStringContainsString(
            '<PhoneTypeEnum xsi:type="ns2:PhoneTypeEnum">Home</PhoneTypeEnum>',
            $lastRequestInfo->getLastRequest()
        );
        $this->assertStringContainsString(
            '<output xsi:type="ns2:PhoneTypeEnum">Home</output>',
            $lastRequestInfo->getLastResponse()
        );
    }
}

enum OfficeEnum : string {
    case HOME = 'Home';
    case GSM = 'Gsm';
    case OFFICE = 'Office';

    /**
     * sadly __toString is not possible on enums.
     * Otherwise, soap would just be able to use php enums...
     */
}
