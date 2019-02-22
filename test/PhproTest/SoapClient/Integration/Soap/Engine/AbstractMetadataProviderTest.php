<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataProviderInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;

abstract class AbstractMetadataProviderTest extends AbstractIntegrationTest
{
    abstract protected function getMetadataProvider(): MetadataProviderInterface;

    /** @test */
    public function it_can_load_wsdl_methods()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/string.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $methods = $metadata->getMethods();

        $this->assertCount(1, $methods);
        $this->assertMethodExists(
            $methods,
            'validate',
            [
                new Parameter('input', XsdType::create('string'))
            ],
            XsdType::create('string')
        );
    }

    /** @test */
    function it_can_load_wsdl_method_with_multiple_response_arguments()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/multiArgumentResponse.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $methods = $metadata->getMethods();

        $this->assertCount(1, $methods);
        $this->assertMethodExists(
            $methods,
            'validate',
            [
                new Parameter('input', XsdType::create('string'))
            ],
            XsdType::create('array')
        );
    }

    /** @test */
    function it_can_load_union_types_in_methods()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/union.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $methods = $metadata->getMethods();

        $jeansType = XsdType::create('jeansSize')
            ->withBaseType('anyType')
            ->withMemberTypes(['sizebyno', 'sizebystring']);

        $this->assertCount(1, $methods);
        $this->assertMethodExists(
            $methods,
            'validate',
            [
                new Parameter('input', $jeansType)
            ],
            $jeansType
        );
    }

    /** @test */
    function it_can_load_list_types_in_methods()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/list.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $methods = $metadata->getMethods();

        $listType = XsdType::create('valuelist')
            ->withBaseType('array')
            ->withMemberTypes(['integer']);

        $this->assertCount(1, $methods);
        $this->assertMethodExists(
            $methods,
            'validate',
            [
                new Parameter('input', $listType)
            ],
            $listType
        );
    }

    /** @test */
    function it_can_load_simple_content_types()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/simpleContent.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $types = $metadata->getTypes();

        $this->assertCount(1, $types);
        $this->assertTypeExists(
            $types,
            XsdType::create('SimpleContent'),
            [
                new Property('_', XsdType::create('integer')),
                new Property('country', XsdType::create('string')),
            ]
        );
    }

    /** @test */
    function it_can_load_complex_types()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/complex-type-request-response.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $types = $metadata->getTypes();

        $this->assertCount(2, $types);
        $this->assertTypeExists(
            $types,
            XsdType::create('ValidateRequest'),
            [
                new Property('input', XsdType::create('string'))
            ]
        );
        $this->assertTypeExists(
            $types,
            XsdType::create('ValidateResponse'),
            [
                new Property('output', XsdType::create('string'))
            ]
        );
    }

    /** @test */
    function it_can_load_union_types()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/union.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $types = $metadata->getTypes();

        $jeansType = XsdType::create('jeansSize')
            ->withBaseType('anyType')
            ->withMemberTypes(['sizebyno', 'sizebystring']);

        $this->assertCount(1, $types);
        $this->assertTypeExists(
            $types,
            XsdType::create('jeansSizeContainer'),
            [
                new Property('jeansSize', $jeansType)
            ]
        );
    }

    /** @test */
    function it_can_load_list_types()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/list.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $types = $metadata->getTypes();

        $listType = XsdType::create('valuelist')
           ->withBaseType('array')
           ->withMemberTypes(['integer']);

        $this->assertCount(1, $types);
        $this->assertTypeExists(
            $types,
            XsdType::create('valuelistContainer'),
            [
                new Property('valuelist', $listType)
            ]
        );
    }

    /** @test */
    function it_can_handle_duplicate_type_declarations()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/duplicate-typenames.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $types = $metadata->getTypes();

        $this->assertCount(2, $types);

        $type1 = $types->getIterator()[1];
        $this->assertSame('Store', $type1->getName());
        $this->assertEquals(XsdType::create('Store'), $type1->getXsdType());
        $this->assertEquals([new Property('Attribute2', XsdType::create('string'))], $type1->getProperties());

        $type2 = $types->getIterator()[1];
        $this->assertSame('Store', $type2->getName());
        $this->assertEquals(XsdType::create('Store'), $type2->getXsdType());
        $this->assertEquals([new Property('Attribute2', XsdType::create('string'))], $type2->getProperties());
    }
    
    private function assertMethodExists(MethodCollection $methods, string $name, array $parameters, XsdType $returnType)
    {
        $method = $methods->fetchOneByName($name);
        $this->assertSame($name, $method->getName());
        $this->assertEquals($parameters, $method->getParameters());
        $this->assertEquals($returnType, $method->getReturnType());
    }

    private function assertTypeExists(TypeCollection $types, XsdType $xsdType, array $properties)
    {
        $type = $types->fetchOneByName($xsdType->getName());
        $this->assertSame($xsdType->getName(), $type->getName());
        $this->assertEquals($xsdType->getName(), $type->getXsdType());
        $this->assertEquals($properties, $type->getProperties());
    }
}
