<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Engine\Metadata\Manipulators;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PHPUnit\Framework\TestCase;

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
                        [new Type(XsdType::create('Response'), [])]
                    ));
                }
            },
            new class implements TypesManipulatorInterface {
                public function __invoke(TypeCollection $allTypes): TypeCollection
                {
                    return new TypeCollection(...array_merge(
                        iterator_to_array($allTypes),
                        [new Type(XsdType::create('Response2'), [])]
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
                new Type(XsdType::create('Response'), []),
                new Type(XsdType::create('Response2'), []),
            ],
            iterator_to_array($result)
        );
    }
}
