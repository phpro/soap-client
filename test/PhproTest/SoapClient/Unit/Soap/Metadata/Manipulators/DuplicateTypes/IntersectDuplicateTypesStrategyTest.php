<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\Type;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

class IntersectDuplicateTypesStrategyTest extends TestCase
{
    #[Test]
    public function it_is_a_types_manipulator(): void
    {
        $strategy = new IntersectDuplicateTypesStrategy();
        self::assertInstanceOf(TypesManipulatorInterface::class, $strategy);
    }

    #[Test]
    public function it_provides_a_factory(): void
    {
        $factory = IntersectDuplicateTypesStrategy::create();
        self::assertInstanceOf(\Closure::class, $factory);

        $strategy = $factory(null);
        self::assertInstanceOf(IntersectDuplicateTypesStrategy::class, $strategy);
    }

    #[Test]
    public function it_can_intersect_duplicate_types(): void
    {
        $strategy = new IntersectDuplicateTypesStrategy();
        $types = new TypeCollection(
            new Type(XsdType::create('file'), new PropertyCollection(
                new Property('prop1', XsdType::create('string')),
                new Property('prop3', XsdType::create('string')),
                new Property('prop4', XsdType::create('string')),
            )),
            new Type(XsdType::create('file'), new PropertyCollection(
                new Property('prop1', XsdType::create('int')),
                new Property('prop2', XsdType::create('string')),
            )),
            new Type(XsdType::create('uppercased'), new PropertyCollection()),
            new Type(XsdType::create('Uppercased'), new PropertyCollection()),
            new Type(XsdType::create('with-specialchar'), new PropertyCollection()),
            new Type(XsdType::create('with*specialchar'), new PropertyCollection()),
            new Type(XsdType::create('not-duplicate'), new PropertyCollection()),
            new Type(XsdType::create('CASEISDIFFERENT'), new PropertyCollection()),
            new Type(XsdType::create('Case-is-different'), new PropertyCollection())
        );

        $manipulated = $strategy($types);
        $nullable = static fn(TypeMeta $meta) => $meta->withIsNullable(true);

        self::assertInstanceOf(TypeCollection::class, $manipulated);
        self::assertEquals(
            [
                new Type(XsdType::create('file'), new PropertyCollection(
                    new Property('prop1', XsdType::create('int')),
                    new Property('prop3', XsdType::create('string')->withMeta($nullable)),
                    new Property('prop4', XsdType::create('string')->withMeta($nullable)),
                    new Property('prop2', XsdType::create('string')->withMeta($nullable)),
                )),
                new Type(XsdType::create('uppercased'), new PropertyCollection()),
                new Type(XsdType::create('with-specialchar'), new PropertyCollection()),
                new Type(XsdType::create('not-duplicate'), new PropertyCollection()),
                new Type(XsdType::create('CASEISDIFFERENT'), new PropertyCollection()),
                new Type(XsdType::create('Case-is-different'), new PropertyCollection()),
            ],
            iterator_to_array($manipulated)
        );
    }

    #[Test]
    public function it_keeps_types_with_same_name_but_different_target_namespace(): void
    {
        $namespaceMap = TypeNamespaceMap::create(new Destination('/path', 'App\\Types'))
            ->withMapping('http://ns-a.com', new Destination('/path/a', 'App\\Types\\A'))
            ->withMapping('http://ns-b.com', new Destination('/path/b', 'App\\Types\\B'));

        $strategy = new IntersectDuplicateTypesStrategy($namespaceMap);

        $types = new TypeCollection(
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-a.com'),
                new PropertyCollection(new Property('propA', XsdType::create('string')))
            ),
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-b.com'),
                new PropertyCollection(new Property('propB', XsdType::create('string')))
            ),
        );

        $manipulated = $strategy($types);

        self::assertCount(2, iterator_to_array($manipulated));
    }

    #[Test]
    public function it_still_intersects_types_in_same_target_namespace(): void
    {
        $namespaceMap = TypeNamespaceMap::create(new Destination('/path', 'App\\Types'));

        $strategy = new IntersectDuplicateTypesStrategy($namespaceMap);

        $types = new TypeCollection(
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-a.com'),
                new PropertyCollection(new Property('propA', XsdType::create('string')))
            ),
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-b.com'),
                new PropertyCollection(new Property('propB', XsdType::create('string')))
            ),
        );

        $manipulated = $strategy($types);

        // Both map to fallback namespace, so they ARE duplicates
        self::assertCount(1, iterator_to_array($manipulated));
    }

    #[Test]
    public function it_behaves_as_before_without_namespace_map(): void
    {
        $strategy = new IntersectDuplicateTypesStrategy(); // No namespace map

        $types = new TypeCollection(
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-a.com'),
                new PropertyCollection()
            ),
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-b.com'),
                new PropertyCollection()
            ),
        );

        $manipulated = $strategy($types);

        // Without namespace map, these are considered duplicates (legacy behavior)
        self::assertCount(1, iterator_to_array($manipulated));
    }
}
