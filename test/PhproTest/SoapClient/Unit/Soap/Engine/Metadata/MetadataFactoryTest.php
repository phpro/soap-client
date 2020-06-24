<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Engine\Metadata;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\MethodsManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataFactory;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataOptions;
use PHPUnit\Framework\TestCase;

class MetadataFactoryTest extends TestCase
{
    /** @test */
    public function it_can_create_lazy_in_memory_metadata(): void
    {
        $meta = new class implements MetadataInterface {
            public function getTypes(): TypeCollection
            {
                return new TypeCollection();
            }

            public function getMethods(): MethodCollection
            {
                return new MethodCollection();
            }
        };
        $lazy = MetadataFactory::lazy($meta);

        self::assertEquals($meta->getTypes(), $lazy->getTypes());
        self::assertEquals($meta->getMethods(), $lazy->getMethods());
        self::assertNotSame($meta->getTypes(), $lazy->getTypes());
        self::assertNotSame($meta->getMethods(), $lazy->getMethods());
        self::assertSame($lazy->getTypes(), $lazy->getTypes());
        self::assertSame($lazy->getMethods(), $lazy->getMethods());
    }

    /** @test */
    public function it_can_create_manipulated_metadata(): void
    {
        $meta = new class implements MetadataInterface {
            public function getTypes(): TypeCollection
            {
                return new TypeCollection();
            }

            public function getMethods(): MethodCollection
            {
                return new MethodCollection();
            }
        };

        $expectedMethods = new MethodCollection();
        $expectedTypes = new TypeCollection();
        $metaOptions = $this->createHardCodedManipulatorOptions($expectedMethods, $expectedTypes);

        $manipulated = MetadataFactory::manipulated($meta, $metaOptions);

        self::assertNotSame($meta->getTypes(), $manipulated->getTypes());
        self::assertNotSame($meta->getMethods(), $manipulated->getMethods());
        self::assertSame($expectedTypes, $manipulated->getTypes());
        self::assertSame($expectedMethods, $manipulated->getMethods());
        self::assertSame($manipulated->getTypes(), $manipulated->getTypes());
        self::assertSame($manipulated->getMethods(), $manipulated->getMethods());
    }

    private function createHardCodedManipulatorOptions(
        MethodCollection $expectedMethods,
        TypeCollection $expectedTypes
    ) {
        return MetadataOptions::empty()
            ->withTypesManipulator(new class ($expectedTypes) implements TypesManipulatorInterface {
                /**
                 * @var TypeCollection
                 */
                private $types;

                public function __construct(TypeCollection $types)
                {
                    $this->types = $types;
                }

                public function __invoke(TypeCollection $allTypes): TypeCollection
                {
                    return $this->types;
                }
            })
            ->withMethodsManipulator(new class ($expectedMethods) implements MethodsManipulatorInterface {

                /**
                 * @var MethodCollection
                 */
                private $methods;

                public function __construct(MethodCollection $methods)
                {
                    $this->methods = $methods;
                }

                public function __invoke(MethodCollection $allMethods): MethodCollection
                {
                    return $this->methods;
                }
            });
    }
}
