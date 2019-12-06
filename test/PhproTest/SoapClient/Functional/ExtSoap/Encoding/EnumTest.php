<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional\ExtSoap\Encoding;

use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapServerHandle;
use Phpro\SoapClient\Soap\Engine\Engine;
use PhproTest\SoapClient\Functional\ExtSoap\AbstractSoapTestCase;

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
     * @var ExtSoapServerHandle
     */
    private $handler;

    protected function setUp(): void
    {
        $this->wsdl = FIXTURE_DIR . '/wsdl/functional/enum.wsdl';
        $this->driver = $this->configureSoapDriver($this->wsdl, []);
        $this->handler = $this->configureServer(
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
        $engine = new Engine($this->driver, $this->handler);
        $response = (string) $engine->request('validate', [$input]);
        $lastRequestInfo = $this->handler->collectLastRequestInfo();

        $this->assertEquals($input, $response);
        $this->assertContains('<input xsi:type="ns2:PhoneTypeEnum">Home</input>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<output xsi:type="ns2:PhoneTypeEnum">Home</output>', $lastRequestInfo->getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_does_not_validate_enums()
    {
        $input = 'INVALID';
        $engine = new Engine($this->driver, $this->handler);
        $engine->request('validate', [$input]);
        $lastRequestInfo = $this->handler->collectLastRequestInfo();

        $this->assertContains('<input xsi:type="ns2:PhoneTypeEnum">INVALID</input>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<output xsi:type="ns2:PhoneTypeEnum">INVALID</output>', $lastRequestInfo->getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_does_not_validate_enum_types()
    {
        $input = 123;
        $engine = new Engine($this->driver, $this->handler);
        $engine->request('validate', [$input]);
        $lastRequestInfo = $this->handler->collectLastRequestInfo();

        $this->assertContains('<input xsi:type="ns2:PhoneTypeEnum">123</input>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<output xsi:type="ns2:PhoneTypeEnum">123</output>', $lastRequestInfo->getLastResponse());
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

                        return $this->createEnum($doc->textContent);
                    },
                    'to_xml' => function($enum) {
                        return sprintf('<PhoneTypeEnum>%s</PhoneTypeEnum>', $enum);
                    },
                ]
            ]
        ]);
        $engine = new Engine($this->driver, $this->handler);

        $input = $this->createEnum('Home');
        $response = $engine->request('validate', [$input]);
        $lastRequestInfo = $this->handler->collectLastRequestInfo();

        $this->assertEquals($input, $response);
        $this->assertContains('<PhoneTypeEnum xsi:type="ns2:PhoneTypeEnum">Home</PhoneTypeEnum>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<output xsi:type="ns2:PhoneTypeEnum">Home</output>', $lastRequestInfo->getLastResponse());
    }

    private function createEnum(string $value) {
        return new class ($value) {
            /**
             * @var string
             */
            private $value;

            public function __construct(string $value)
            {
                if (!in_array($value, ['Home', 'Office', 'Gsm'], true)) {
                    throw new \Exception('Unknown enum value ' . $value);
                }

                $this->value = $value;
            }

            public function __toString()
            {
                return $this->value;
            }
        };
    }
}
