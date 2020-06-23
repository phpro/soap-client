<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Engine\Metadata;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\ManipulatedMetadata;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\MethodsManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\MethodsManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PHPUnit\Framework\TestCase;

class ManipulatedMetadataTest extends TestCase
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    protected function setUp(): void
    {
        $this->metadata = new class implements MetadataInterface
        {
            public function getTypes(): TypeCollection
            {
                return new TypeCollection();
            }

            public function getMethods(): MethodCollection
            {
                return new MethodCollection();
            }
        };
    }

    /** @test */
    public function it_is_a_metdata_object(): void
    {
        self::assertInstanceOf(MetadataInterface::class, new ManipulatedMetadata(
            $this->metadata,
            new MethodsManipulatorChain(),
            new TypesManipulatorChain()
        ));
    }

    /** @test */
    public function it_proxies_everything_on_no_manipulators(): void
    {
        $manipulator = new ManipulatedMetadata(
            $this->metadata,
            new MethodsManipulatorChain(),
            new TypesManipulatorChain()
        );

        self::assertEquals($this->metadata->getMethods(), $manipulator->getMethods());
        self::assertEquals($this->metadata->getTypes(), $manipulator->getTypes());
    }

    /** @test */
    public function it_can_manipulate_methods(): void
    {
        $manipulatedMeta = new ManipulatedMetadata(
            $this->metadata,
            new class implements MethodsManipulatorInterface {
                public function __invoke(MethodCollection $allMethods): MethodCollection
                {
                    return new MethodCollection(new Method('method', [], XsdType::create('Response')));
                }

            },
            new TypesManipulatorChain()
        );

        self::assertEquals(
            new MethodCollection(new Method('method', [], XsdType::create('Response'))),
            $manipulatedMeta->getMethods()
        );
    }

    /** @test */
    public function it_can_manipulate_types(): void
    {
        $manipulatedMeta = new ManipulatedMetadata(
            $this->metadata,
            new MethodsManipulatorChain(),
            new class implements TypesManipulatorInterface {
                public function __invoke(TypeCollection $allTypes): TypeCollection
                {
                    return new TypeCollection(new Type(XsdType::create('Type'), []));
                }
            }
        );

        self::assertEquals(
            new TypeCollection(new Type(XsdType::create('Type'), [])),
            $manipulatedMeta->getTypes()
        );
    }
}
