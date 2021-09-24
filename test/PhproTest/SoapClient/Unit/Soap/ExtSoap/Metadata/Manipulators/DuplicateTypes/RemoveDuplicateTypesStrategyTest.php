<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorInterface;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type;
use Soap\Engine\Metadata\Model\XsdType;

class RemoveDuplicateTypesStrategyTest extends TestCase
{
    public function it_is_a_types_manipulator(): void
    {
        $strategy = new RemoveDuplicateTypesStrategy();
        self::assertInstanceOf(TypesManipulatorInterface::class, $strategy);
    }

    /** @test */
    public function it_can_intersect_duplicate_types(): void
    {
        $strategy = new RemoveDuplicateTypesStrategy();
        $types = new TypeCollection(
            new Type(XsdType::create('file'), new PropertyCollection()),
            new Type(XsdType::create('file'), new PropertyCollection()),
            new Type(XsdType::create('uppercased'), new PropertyCollection()),
            new Type(XsdType::create('Uppercased'), new PropertyCollection()),
            new Type(XsdType::create('with-specialchar'), new PropertyCollection()),
            new Type(XsdType::create('with*specialchar'), new PropertyCollection()),
            new Type(XsdType::create('not-duplicate'), new PropertyCollection()),
            new Type(XsdType::create('CASEISDIFFERENT'), new PropertyCollection()),
            new Type(XsdType::create('Case-is-different'), new PropertyCollection())
        );

        $manipulated = $strategy($types);

        self::assertInstanceOf(TypeCollection::class, $manipulated);
        self::assertEquals(
            [
                new Type(XsdType::create('not-duplicate'), new PropertyCollection()),
                new Type(XsdType::create('CASEISDIFFERENT'), new PropertyCollection()),
                new Type(XsdType::create('Case-is-different'), new PropertyCollection()),
            ],
            iterator_to_array($manipulated)
        );
    }
}
