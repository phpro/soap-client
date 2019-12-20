<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Functional\ExtSoap\Encoding;

use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapServerHandle;
use Phpro\SoapClient\Soap\Engine\Engine;
use PhproTest\SoapClient\Functional\ExtSoap\AbstractSoapTestCase;

class DuplicateTypenamesTest extends AbstractSoapTestCase
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
        $this->wsdl = FIXTURE_DIR.'/wsdl/functional/duplicate-typenames.wsdl';
        $this->driver = $this->configureSoapDriver($this->wsdl, []);
        $this->handler = $this->configureServer(
            $this->wsdl,
            [],
            new class()
            {
                public function validate($store1, $store2)
                {
                    return ['output1' => $store1, 'output2' => $store2];
                }
            }
        );
    }

    /** @test */
    function it_registers_both_types()
    {
        $types = $this->driver->getMetadata()->getTypes();
        $this->assertCount(2, $types);

        $store1 = $types->getIterator()[0];
        $store2 = $types->getIterator()[1];

        $this->assertEquals($store1->getName(), 'Store');
        $this->assertEquals($store1->getProperties()[0]->getName(), 'Attribute1');
        $this->assertEquals($store2->getName(), 'Store');
        $this->assertEquals($store2->getProperties()[0]->getName(), 'Attribute2');
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_knows_how_to_encode_both_types()
    {
        $engine = new Engine($this->driver, $this->handler);
        $store1 = (object) ['Attribute1' => 'ok'];
        $store2 = (object) ['Attribute2' => 'ok'];

        $response = $engine->request('validate', [$store1, $store2]);

        $this->assertEquals($store1, $response['output1']);
        $this->assertEquals($store2, $response['output2']);

        $lastRequestInfo = $this->handler->collectLastRequestInfo();
        $this->assertContains('<input1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">ok</Attribute1></input1>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<input2 xsi:type="ns3:Store"><Attribute2 xsi:type="xsd:string">ok</Attribute2></input2>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<output1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">ok</Attribute1></output1>', $lastRequestInfo->getLastResponse());
        $this->assertContains('<output2 xsi:type="ns3:Store"><Attribute2 xsi:type="xsd:string">ok</Attribute2></output2>', $lastRequestInfo->getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_uses_same_model_for_both_objects()
    {
        $this->driver = $this->configureSoapDriver($this->wsdl, [
            'classmap' => new ClassMapCollection([
                new ClassMap('Store', DuplicateTypeStore::class)
            ])
        ]);
        $this->handler = $this->configureServer(
            $this->wsdl,
            [],
            new class()
            {
                public function validate($store1, $store2)
                {
                    return [
                        'output1' => new DuplicateTypeStore('attr1', null),
                        'output2' => new DuplicateTypeStore(null, 'attr2')
                    ];
                }
            }
        );

        $engine = new Engine($this->driver, $this->handler);
        $store1 = new DuplicateTypeStore('attr1', 'attr2');
        $store2 = new DuplicateTypeStore('attr1', 'attr2');
        $response = $engine->request('validate', [$store1, $store2]);
        $lastRequestInfo = $this->handler->collectLastRequestInfo();

        $this->assertEquals(new DuplicateTypeStore('attr1', null), $response['output1']);
        $this->assertEquals(new DuplicateTypeStore(null, 'attr2'), $response['output2']);
        $this->assertContains('<input1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></input1>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<input2 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></input2>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<output1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></output1>', $lastRequestInfo->getLastResponse());
        $this->assertContains('<output2 xsi:type="ns3:Store"><Attribute2 xsi:type="xsd:string">attr2</Attribute2></output2>', $lastRequestInfo->getLastResponse());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    function it_is_possible_to_override_a_single_instance_with_typemap()
    {
        $this->driver = $this->configureSoapDriver($this->wsdl, [
            'classmap' => new ClassMapCollection([
                new ClassMap('Store', DuplicateTypeStore::class)
            ]),
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
            ]
        ]);
        $this->handler = $this->configureServer(
            $this->wsdl,
            [],
            new class()
            {
                public function validate($store1, $store2)
                {
                    return [
                        'output1' => new DuplicateTypeStore('attr1', null),
                        'output2' => new DuplicateTypeStore(null, 'attr2')
                    ];
                }
            }
        );

        $engine = new Engine($this->driver, $this->handler);
        $store1 = new DuplicateTypeStore('attr1', 'attr2');
        $store2 = new DuplicateTypeStore('attr1', 'attr2');
        $response = $engine->request('validate', [$store1, $store2]);
        $lastRequestInfo = $this->handler->collectLastRequestInfo();

        $this->assertEquals($this->createStore1Class('attr1'), $response['output1']);
        $this->assertEquals(new DuplicateTypeStore(null, 'attr2'), $response['output2']);
        $this->assertContains('<input1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></input1>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<input2 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></input2>', $lastRequestInfo->getLastRequest());
        $this->assertContains('<output1 xsi:type="ns2:Store"><Attribute1 xsi:type="xsd:string">attr1</Attribute1></output1>', $lastRequestInfo->getLastResponse());
        $this->assertContains('<output2 xsi:type="ns3:Store"><Attribute2 xsi:type="xsd:string">attr2</Attribute2></output2>', $lastRequestInfo->getLastResponse());
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
