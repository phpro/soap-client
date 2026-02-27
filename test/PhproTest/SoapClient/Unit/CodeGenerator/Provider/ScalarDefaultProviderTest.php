<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\CodeGenerator\Provider;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Provider\ScalarDefaultProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

class ScalarDefaultProviderTest extends TestCase
{
    #[DataProvider('provideDefaults')]
    #[Test]
    public function it_provides_default_values(Property $property, bool $expectSuccess, mixed $expectedValue = null): void
    {
        $provider = new ScalarDefaultProvider();
        $result = $provider($property);

        if ($expectSuccess) {
            $this->assertTrue($result->isSucceeded());
            $this->assertSame($expectedValue, $result->getResult());
        } else {
            $this->assertTrue($result->isFailed());
        }
    }

    public static function provideDefaults(): iterable
    {
        $namespaces = TypeNamespaceMap::create(new Destination('/generated', 'ns1'));

        yield 'string' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::create('string'))),
            true,
            '',
        ];

        yield 'int' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::create('int'))),
            true,
            0,
        ];

        yield 'bool' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::create('bool'))),
            true,
            false,
        ];

        yield 'float' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::create('float'))),
            true,
            0.0,
        ];

        yield 'array (list)' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::guess('string')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
            ))),
            true,
            [],
        ];

        yield 'mixed' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::create('mixed'))),
            true,
            null,
        ];

        yield 'nullable complex type' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::create('SomeClass')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsNullable(true)
            ))),
            true,
            null,
        ];

        yield 'non-nullable complex type' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::create('SomeClass'))),
            false,
        ];

        yield 'nullable scalar (nullable check wins)' => [
            Property::fromMetaData($namespaces, new MetaProperty('prop', XsdType::create('string')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsNullable(true)
            ))),
            true,
            null,
        ];
    }
}
