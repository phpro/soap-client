<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional\Encoding;

use PhproTest\SoapClient\Functional\AbstractSoapTestCase;
use SoapServer;

class EnumTest extends AbstractSoapTestCase
{
    protected function configureServer(SoapServer $server)
    {
        $server->setObject(new class() {
            public function validate($input)
            {
                return $input;
            }
        });
    }

    protected function getWsdl(): string
    {
        return FIXTURE_DIR . '/wsdl/functional/enum.wsdl';
    }

    protected function getSoapOptions(): array {
        return $this->provideBasicWsdlOptions();
    }

    /** @test */
    function it_does_not_register_a_type()
    {
        $types = $this->client->getSoapTypes();
        $this->assertCount(0, $types);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_knows_how_to_add_enums()
    {
        $input = 'Home';
        $response = (string) $this->client->validate($input);
        $this->assertEquals($input, $response);

        $this->assertContains('<input xsi:type="ns2:PhoneTypeEnum">Home</input>', $this->client->__getLastRequest());
        $this->assertContains('<output xsi:type="ns2:PhoneTypeEnum">Home</output>', $this->client->__getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_does_not_validate_enums()
    {
        $input = 'INVALID';
        $this->client->validate($input);

        $this->assertContains('<input xsi:type="ns2:PhoneTypeEnum">INVALID</input>', $this->client->__getLastRequest());
        $this->assertContains('<output xsi:type="ns2:PhoneTypeEnum">INVALID</output>', $this->client->__getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_does_not_validate_enum_types()
    {
        $input = 123;
        $this->client->validate($input);

        $this->assertContains('<input xsi:type="ns2:PhoneTypeEnum">123</input>', $this->client->__getLastRequest());
        $this->assertContains('<output xsi:type="ns2:PhoneTypeEnum">123</output>', $this->client->__getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_can_be_transformed_with_type_map()
    {
        $this->configureSoapClient($this->getWsdl(), $this->provideBasicWsdlOptions([
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
                        return sprintf('<PhoneTypeEnum>%s</PhoneTypeEnum>', $enum->__toString());
                    },
                ],
            ],
        ]));

        $input = $this->createEnum('Home');
        $response = $this->client->validate($input);

        $this->assertEquals($input, $response);
        $this->assertContains('<PhoneTypeEnum xsi:type="ns2:PhoneTypeEnum">Home</PhoneTypeEnum>', $this->client->__getLastRequest());
        $this->assertContains('<output xsi:type="ns2:PhoneTypeEnum">Home</output>', $this->client->__getLastResponse());
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
