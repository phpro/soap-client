<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Functional\Encoding;

use PhproTest\SoapClient\Functional\AbstractSoapTestCase;
use SoapServer;

class DuplicateTypenamesTest extends AbstractSoapTestCase
{
    protected function configureServer(SoapServer $server)
    {
        $server->setObject(new class() {
            public function validate($store1, $store2)
            {
                return ['output1' => $store1, 'output2' => $store2];
            }
        });
    }

    protected function getWsdl(): string
    {
        return FIXTURE_DIR . '/wsdl/functional/duplicate-typenames.wsdl';
    }

    protected function getSoapOptions(): array {
        return $this->provideBasicWsdlOptions();
    }

    /** @test */
    function it_does_only_register_the_last_type()
    {
        $types = $this->client->getSoapTypes();
        $this->assertCount(1, $types);

        $type = $types['Store'];

        $this->assertEquals($type['Attribute2'], 'string');
    }

    /**
     * @ test
     * @runInSeparateProcess
     */
    function it_knows_how_to_encode_both_types()
    {
        $store1 = (object) ['Attribute1' => 'ok'];
        $store2 = (object) ['Attribute2' => 'ok'];

        $response = $this->client->validate($store1, $store2);

        $this->assertEquals($store1, $response['output1']);
        $this->assertEquals($store2, $response['output2']);

        $this->assertContains('<input1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">ok</Attribute1></input1>', $this->client->__getLastRequest());
        $this->assertContains('<input2 xsi:type="ns3:Store"><Attribute2 xsi:type="xsd:string">ok</Attribute2></input2>', $this->client->__getLastRequest());
        $this->assertContains('<output1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">ok</Attribute1></output1>', $this->client->__getLastResponse());
        $this->assertContains('<output2 xsi:type="ns3:Store"><Attribute2 xsi:type="xsd:string">ok</Attribute2></output2>', $this->client->__getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_uses_same_model_for_both_objects()
    {
        $this->configureSoapClient($this->getWsdl(), $this->provideBasicWsdlOptions([
            'classmap' => [
                'Store' => DuplicateTypeStore::class,
            ],
        ]));
        $this->server->setObject(new class() {
            public function validate($store1, $store2)
            {
                return [
                    'output1' => new DuplicateTypeStore('attr1', null),
                    'output2' => new DuplicateTypeStore(null, 'attr2')
                ];
            }
        });

        $store1 = new DuplicateTypeStore('attr1', 'attr2');
        $store2 = new DuplicateTypeStore('attr1', 'attr2');
        $response = $this->client->validate($store1, $store2);

        $this->assertEquals(new DuplicateTypeStore('attr1', null), $response['output1']);
        $this->assertEquals(new DuplicateTypeStore(null, 'attr2'), $response['output2']);
        $this->assertContains('<input1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></input1>', $this->client->__getLastRequest());
        $this->assertContains('<input2 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></input2>', $this->client->__getLastRequest());
        $this->assertContains('<output1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></output1>', $this->client->__getLastResponse());
        $this->assertContains('<output2 xsi:type="ns3:Store"><Attribute2 xsi:type="xsd:string">attr2</Attribute2></output2>', $this->client->__getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_is_possible_to_override_a_single_instance_with_typemap()
    {
        $this->configureSoapClient($this->getWsdl(), $this->provideBasicWsdlOptions([
            'classmap' => [
                'Store' => DuplicateTypeStore::class,
            ],
            'typemap' => [
                [
                    'type_name' => 'Store',
                    'type_ns' => 'http://soapinterop.org/xsd1',
                    'from_xml' => function($xml) {
                        $doc = new \DOMDocument();
                        $doc->loadXML($xml);
                        $attr1 = $doc->childNodes->item(0)->textContent;

                        return $this->createStore1Class($attr1);
                    },
                ],
            ],
        ]));
        $this->server->setObject(new class() {
            public function validate($store1, $store2)
            {
                return [
                    'output1' => new DuplicateTypeStore('attr1', null),
                    'output2' => new DuplicateTypeStore(null, 'attr2')
                ];
            }
        });

        $store1 = new DuplicateTypeStore('attr1', 'attr2');
        $store2 = new DuplicateTypeStore('attr1', 'attr2');
        $response = $this->client->validate($store1, $store2);

        $this->assertEquals($this->createStore1Class('attr1'), $response['output1']);
        $this->assertEquals(new DuplicateTypeStore(null, 'attr2'), $response['output2']);
        $this->assertContains('<input1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></input1>', $this->client->__getLastRequest());
        $this->assertContains('<input2 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></input2>', $this->client->__getLastRequest());
        $this->assertContains('<output1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></output1>', $this->client->__getLastResponse());
        $this->assertContains('<output2 xsi:type="ns3:Store"><Attribute2 xsi:type="xsd:string">attr2</Attribute2></output2>', $this->client->__getLastResponse());
    }

    private function createStore1Class($attr1) {
        return new class($attr1) {
            private $Attribute1;
            public function __construct($Attribute1)
            {
                $this->Attribute1 = $Attribute1;
            }
        };
    }
}


class DuplicateTypeStore {
    public $Attribute1;
    public $Attribute2;

    public function __construct($Attribute1, $Attribute2)
    {
        $this->Attribute1 = $Attribute1;
        $this->Attribute2 = $Attribute2;
    }
}
