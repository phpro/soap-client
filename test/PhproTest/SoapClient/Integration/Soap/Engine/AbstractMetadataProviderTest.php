<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataProviderInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;

abstract class AbstractMetadataProviderTest extends AbstractIntegrationTest
{
    abstract protected function getMetadataProvider(): MetadataProviderInterface;

    /** @test */
    public function it_can_load_wsdl_methods()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/stringContent.wsdl');

        $metadata = $this->getMetadataProvider()->getMetadata();
        $methods = $metadata->getMethods();

        $this->assertCount(1, $methods);
        $this->assertMethodExists(
            $methods,
            'validate',
            [
                new Parameter('input', 'string')
            ],
            'string'
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
            'SimpleContent',
            [
                new Property('_', 'integer'),
                new Property('country', 'string'),
            ]
        );
    }

    private function assertMethodExists(MethodCollection $methods, string $name, array $parameters, string $returnType)
    {
        $method = $methods->fetchByName($name);
        $this->assertSame($name, $method->getName());
        $this->assertEquals($parameters, $method->getParameters());
        $this->assertSame($returnType, $method->getReturnType());
    }

    private function assertTypeExists(TypeCollection $types, string $name, array $properties)
    {
        $type = $types->fetchByName($name);
        $this->assertSame($name, $type->getName());
        $this->assertEquals($properties, $type->getProperties());
    }
}
