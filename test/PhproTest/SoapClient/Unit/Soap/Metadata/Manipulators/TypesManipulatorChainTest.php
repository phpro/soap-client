<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Metadata\Manipulators;

use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorInterface;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type;
use Soap\Engine\Metadata\Model\XsdType;

class TypesManipulatorChainTest extends TestCase
{
    /** @test */
    public function it_is_a_type_manipulator(): void
    {
        self::assertInstanceOf(TypesManipulatorInterface::class, new TypesManipulatorChain());
    }

    /** @test */
    public function it_does_not_touch_types_with_no_manipulator(): void
    {
        $types = new TypeCollection();
        $chain = new TypesManipulatorChain();
        $result = $chain($types);

        self::assertSame($types, $result);
    }

    /** @test */
    public function it_manipulates_types_collection(): void
    {
        $types = new TypeCollection();
        $chain = new TypesManipulatorChain(
            new class implements TypesManipulatorInterface {
                public function __invoke(TypeCollection $allTypes): TypeCollection
                {
                    return new TypeCollection(...array_merge(
                        iterator_to_array($allTypes),
                        [new Type(XsdType::create('Response'), new PropertyCollection())]
                    ));
                }
            },
            new class implements TypesManipulatorInterface {
                public function __invoke(TypeCollection $allTypes): TypeCollection
                {
                    return new TypeCollection(...array_merge(
                        iterator_to_array($allTypes),
                        [new Type(XsdType::create('Response2'), new PropertyCollection())]
                    ));
                }
            }
        );
        $result = $chain($types);

        self::assertNotSame($types, $result);
        self::assertInstanceOf(TypeCollection::class, $result);
        self::assertCount(2, $result);
        self::assertEquals(
            [
                new Type(XsdType::create('Response'), new PropertyCollection()),
                new Type(XsdType::create('Response2'), new PropertyCollection()),
            ],
            iterator_to_array($result)
        );
    }
}
