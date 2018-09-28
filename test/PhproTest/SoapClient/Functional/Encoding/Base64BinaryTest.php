<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional\Encoding;

use PhproTest\SoapClient\Functional\AbstractSoapTestCase;
use SoapServer;
use SoapVar;

class Base64BinaryTest extends AbstractSoapTestCase
{
    protected function configureServer(SoapServer $server)
    {
        $server->setObject(new class() {
            public function validate($input)
            {
                return [
                    'input' => $input,
                    'output' => 'output',
                ];
            }
        });
    }

    protected function getWsdl()
    {
        return FIXTURE_DIR . '/wsdl/functional/base64Binary.wsdl';
    }

    protected function getSoapOptions(): array {
        return $this->provideBasicNonWsdlOptions();
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_automatically_converts_base64_binary_fields()
    {
        $input = 'input';
        $output = 'output';
        $response = (array)$this->client->validate($input);

        $this->assertEquals($output, $response['output']);
        $this->assertEquals($input, $response['input']);
        $this->assertContains(base64_encode($input), $this->client->__getLastRequest());
        $this->assertContains(base64_encode($output), $this->client->__getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_automatically_converts_base64_binary_internal_types()
    {
        $input = 'input';
        $output = 'output';
        $response = (array)$this->client->validate(new SoapVar('input', XSD_BASE64BINARY));

        $this->assertEquals($output, $response['output']);
        $this->assertEquals($input, $response['input']);
        $this->assertContains(base64_encode($input), $this->client->__getLastRequest());
        $this->assertContains(base64_encode($output), $this->client->__getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_is_possible_to_get_the_raw_value_with_type_converter()
    {
        $this->configureSoapClient($this->getWsdl(), $this->provideBasicWsdlOptions([
            'typemap' => [
                [
                    'type_name' => 'base64Binary',
                    'type_ns' => 'http://www.w3.org/2001/XMLSchema',
                    'from_xml' => function($xml) {
                        $doc = new \DOMDocument();
                        $doc->loadXML($xml);

                        if ('' === $doc->textContent) {
                            return null;
                        }

                        return $doc->textContent;
                    },
                    'to_xml' => function($raw) {
                        return sprintf('<base64Binary>%s</base64Binary>', $raw);
                    },
                ],
            ],
        ]));

        $input = base64_encode('input');
        $output = base64_encode('output');
        $response = (array)$this->client->validate($input);

        $this->assertEquals($output, $response['output']);
        $this->assertEquals(base64_decode($input), $response['input']);
        $this->assertContains($input, $this->client->__getLastRequest());
        $this->assertContains($output, $this->client->__getLastResponse());
    }
}
