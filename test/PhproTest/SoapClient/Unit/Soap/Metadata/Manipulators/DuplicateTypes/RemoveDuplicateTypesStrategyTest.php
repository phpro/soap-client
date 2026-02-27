<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\RemoveDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type;
use Soap\Engine\Metadata\Model\XsdType;

class RemoveDuplicateTypesStrategyTest extends TestCase
{
    private function createContext(?TypeNamespaceMap $namespaceMap = null): CodeGeneratorContext
    {
        return new CodeGeneratorContext(
            $namespaceMap ?? TypeNamespaceMap::create(new Destination('/generated', 'Generated')),
            new DefaultCodingStandardsStrategy(),
        );
    }

    #[Test]
    public function it_is_a_types_manipulator(): void
    {
        $strategy = new RemoveDuplicateTypesStrategy($this->createContext());
        self::assertInstanceOf(TypesManipulatorInterface::class, $strategy);
    }

    #[Test]
    public function it_provides_a_factory(): void
    {
        $factory = RemoveDuplicateTypesStrategy::create();
        self::assertInstanceOf(\Closure::class, $factory);

        $strategy = $factory($this->createContext());
        self::assertInstanceOf(RemoveDuplicateTypesStrategy::class, $strategy);
    }

    #[Test]
    public function it_can_remove_duplicate_types(): void
    {
        $strategy = new RemoveDuplicateTypesStrategy($this->createContext());
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

    #[Test]
    public function it_keeps_types_with_same_name_but_different_target_namespace(): void
    {
        $namespaceMap = TypeNamespaceMap::create(new Destination('/path', 'App\\Types'))
            ->withMapping('http://ns-a.com', new Destination('/path/a', 'App\\Types\\A'))
            ->withMapping('http://ns-b.com', new Destination('/path/b', 'App\\Types\\B'));

        $strategy = new RemoveDuplicateTypesStrategy($this->createContext($namespaceMap));

        $types = new TypeCollection(
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-a.com'),
                new PropertyCollection()
            ),
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-b.com'),
                new PropertyCollection()
            ),
            new Type(
                XsdType::create('UniqueType')->withXmlNamespace('http://ns-a.com'),
                new PropertyCollection()
            ),
        );

        $manipulated = $strategy($types);

        // All three should remain: two Items are in different target namespaces, UniqueType is unique
        self::assertCount(3, iterator_to_array($manipulated));
    }

    #[Test]
    public function it_still_removes_types_in_same_target_namespace(): void
    {
        $namespaceMap = TypeNamespaceMap::create(new Destination('/path', 'App\\Types'));

        $strategy = new RemoveDuplicateTypesStrategy($this->createContext($namespaceMap));

        $types = new TypeCollection(
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-a.com'),
                new PropertyCollection()
            ),
            new Type(
                XsdType::create('Item')->withXmlNamespace('http://ns-b.com'),
                new PropertyCollection()
            ),
            new Type(
                XsdType::create('UniqueType')->withXmlNamespace('http://ns-a.com'),
                new PropertyCollection()
            ),
        );

        $manipulated = $strategy($types);

        // Both Items map to fallback namespace, so they ARE duplicates and get removed
        // Only UniqueType remains
        self::assertCount(1, iterator_to_array($manipulated));
        self::assertEquals('UniqueType', iterator_to_array($manipulated)[0]->getName());
    }
}
