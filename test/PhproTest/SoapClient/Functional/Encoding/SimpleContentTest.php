<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional\Encoding;

use PhproTest\SoapClient\Functional\AbstractSoapTestCase;
use SoapServer;

class SimpleContentTest extends AbstractSoapTestCase
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
        return FIXTURE_DIR . '/wsdl/functional/simpleContent.wsdl';
    }

    protected function getSoapOptions(): array {
        return $this->provideBasicWsdlOptions();
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_uses_underscores_internally_as_node_value_of_simple_content()
    {
        $input = $output = ['_' => 132, 'country' => 'BE'];
        $response = (array) $this->client->validate($input);

        $this->assertEquals($output, $response);
        $this->assertContains('<input xsi:type="ns2:SimpleContent" country="BE">132</input>', $this->client->__getLastRequest());
        $this->assertContains('<output xsi:type="ns2:SimpleContent" country="BE">132</output>', $this->client->__getLastResponse());
    }
}
