<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Driver\ExtSoap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;
use PHPUnit\Framework\TestCase;

class IntersectDuplicateTypesStrategyTest extends TestCase
{
    public function it_is_a_types_manipulator(): void
    {
        $strategy = new IntersectDuplicateTypesStrategy();
        self::assertInstanceOf(TypesManipulatorInterface::class, $strategy);
    }

    /** @test */
    public function it_can_intersect_duplicate_types(): void
    {
        $strategy = new IntersectDuplicateTypesStrategy();
        $types = new TypeCollection(
            new Type(XsdType::create('file'), [
                new Property('prop1', XsdType::create('string')),
                new Property('prop3', XsdType::create('string')),
            ]),
            new Type(XsdType::create('file'), [
                new Property('prop1', XsdType::create('string')),
                new Property('prop2', XsdType::create('string')),
            ]),
            new Type(XsdType::create('uppercased'), []),
            new Type(XsdType::create('Uppercased'), []),
            new Type(XsdType::create('with-specialchar'), []),
            new Type(XsdType::create('with*specialchar'), []),
            new Type(XsdType::create('not-duplicate'), []),
            new Type(XsdType::create('CASEISDIFFERENT'), []),
            new Type(XsdType::create('Case-is-different'), [])
        );

        $manipulated = $strategy($types);

        self::assertInstanceOf(TypeCollection::class, $manipulated);
        self::assertEquals(
            [
                new Type(XsdType::create('file'), [
                    new Property('prop1', XsdType::create('string')),
                    new Property('prop3', XsdType::create('string')),
                    new Property('prop2', XsdType::create('string')),
                ]),
                new Type(XsdType::create('uppercased'), []),
                new Type(XsdType::create('with-specialchar'), []),
                new Type(XsdType::create('not-duplicate'), []),
                new Type(XsdType::create('CASEISDIFFERENT'), []),
                new Type(XsdType::create('Case-is-different'), []),
            ],
            iterator_to_array($manipulated)
        );
    }
}
