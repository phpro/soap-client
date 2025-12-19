<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes\DuplicateTypesKey;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Model\Type;
use Soap\Engine\Metadata\Model\XsdType;

class DuplicateTypesKeyTest extends TestCase
{
    #[Test]
    public function it_returns_normalized_name_without_namespace_map(): void
    {
        $type = new Type(XsdType::create('MyType'), new PropertyCollection());

        $key = DuplicateTypesKey::forType($type, null);

        self::assertEquals('MyType', $key);
    }

    #[Test]
    public function it_normalizes_class_name(): void
    {
        $type = new Type(XsdType::create('my-type'), new PropertyCollection());

        $key = DuplicateTypesKey::forType($type, null);

        self::assertEquals('MyType', $key);
    }

    #[Test]
    public function it_includes_target_namespace_with_namespace_map(): void
    {
        $type = new Type(
            XsdType::create('MyType')->withXmlNamespace('http://ns-a.com'),
            new PropertyCollection()
        );

        $namespaceMap = TypeNamespaceMap::create(new Destination('/path', 'App\\Types'))
            ->withMapping('http://ns-a.com', new Destination('/path/a', 'App\\Types\\A'));

        $key = DuplicateTypesKey::forType($type, $namespaceMap);

        self::assertEquals('App\\Types\\A\\MyType', $key);
    }

    #[Test]
    public function it_uses_fallback_namespace_for_unmapped_xmlns(): void
    {
        $type = new Type(
            XsdType::create('MyType')->withXmlNamespace('http://unmapped.com'),
            new PropertyCollection()
        );

        $namespaceMap = TypeNamespaceMap::create(new Destination('/path', 'App\\Types'))
            ->withMapping('http://ns-a.com', new Destination('/path/a', 'App\\Types\\A'));

        $key = DuplicateTypesKey::forType($type, $namespaceMap);

        self::assertEquals('App\\Types\\MyType', $key);
    }

    #[Test]
    public function same_name_different_xmlns_produces_different_keys(): void
    {
        $typeA = new Type(
            XsdType::create('Item')->withXmlNamespace('http://ns-a.com'),
            new PropertyCollection()
        );
        $typeB = new Type(
            XsdType::create('Item')->withXmlNamespace('http://ns-b.com'),
            new PropertyCollection()
        );

        $namespaceMap = TypeNamespaceMap::create(new Destination('/path', 'App\\Types'))
            ->withMapping('http://ns-a.com', new Destination('/path/a', 'App\\Types\\A'))
            ->withMapping('http://ns-b.com', new Destination('/path/b', 'App\\Types\\B'));

        $keyA = DuplicateTypesKey::forType($typeA, $namespaceMap);
        $keyB = DuplicateTypesKey::forType($typeB, $namespaceMap);

        self::assertNotEquals($keyA, $keyB);
        self::assertEquals('App\\Types\\A\\Item', $keyA);
        self::assertEquals('App\\Types\\B\\Item', $keyB);
    }

    #[Test]
    public function same_name_same_target_namespace_produces_same_key(): void
    {
        $typeA = new Type(
            XsdType::create('Item')->withXmlNamespace('http://ns-a.com'),
            new PropertyCollection()
        );
        $typeB = new Type(
            XsdType::create('Item')->withXmlNamespace('http://ns-b.com'),
            new PropertyCollection()
        );

        // Both namespaces map to fallback (no specific mappings)
        $namespaceMap = TypeNamespaceMap::create(new Destination('/path', 'App\\Types'));

        $keyA = DuplicateTypesKey::forType($typeA, $namespaceMap);
        $keyB = DuplicateTypesKey::forType($typeB, $namespaceMap);

        self::assertEquals($keyA, $keyB);
        self::assertEquals('App\\Types\\Item', $keyA);
    }
}
