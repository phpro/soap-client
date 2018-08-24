<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional\Encoding;

use PhproTest\SoapClient\Functional\AbstractSoapTestCase;
use SoapServer;

class XmlEntitiesTest extends AbstractSoapTestCase
{
    protected function configureServer(SoapServer $server)
    {
        $server->setObject(new class() {
            public function validate($input)
            {
                return [
                    'input' => $input,
                    'output' => '<\'"Sômé Spèçìâl Chàrz"\'>',
                ];
            }
        });
    }

    protected function getWsdl()
    {
        return null;
    }

    protected function getSoapOptions(): array {
        return $this->provideBasicNonWsdlOptions();
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_encodes_special_charakters()
    {
        $input = '<\'"ïnpüt"\'>';
        $output = '<\'"Sômé Spèçìâl Chàrz"\'>';

        $response = $this->client->validate($input);
        $this->assertEquals($input, $response['input']);
        $this->assertEquals($output, $response['output']);

        $this->assertContains(htmlspecialchars($input, ENT_NOQUOTES), $this->client->__getLastRequest());
        $this->assertContains(htmlspecialchars($output, ENT_NOQUOTES), $this->client->__getLastResponse());
    }
}
