<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Psl\Option\Option;
use Soap\Engine\Metadata\Model\Property as EngineProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class PropertyTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Model
 */
class PropertyTest extends TestCase
{
    private static function createTypeNamespaceMap(string $namespace): TypeNamespaceMap
    {
        return TypeNamespaceMap::create(new Destination('/generated', $namespace));
    }

    #[Test]
    public function it_returns_mixed_type_post_php8(): void
    {
        $property = new Property('test', 'mixed', self::createTypeNamespaceMap('App'), 'App', XsdType::create('mixed'));
        self::assertEquals('mixed', $property->getPhpType());
        self::assertEquals('mixed', $property->getType());
    }

    #[Test]
    public function it_can_use_fqcn_to_3rd_party_classes_as_type_name(): void
    {
        $property = Property::fromMetaData(
            self::createTypeNamespaceMap('MyApp'),
            new EngineProperty(
                'property',
                $xsdType = XsdType::create(Option::class)
            )
        );

        self::assertEquals('\\' . Option::class, $property->getType());
        self::assertNotSame($xsdType, $property->getXsdType());
        self::assertSame('Option', $property->getXsdType()->getName());
        self::assertSame('Option', $property->getXsdType()->getBaseType());
    }

    #[Test]
    public function it_can_use_a_php_built_in_class_as_type_name(): void
    {
        $property = Property::fromMetaData(
            self::createTypeNamespaceMap('MyApp'),
            new EngineProperty(
                'property',
                $xsdType = XsdType::create('\\'. \DateInterval::class)
            )
        );

        self::assertEquals('\\' . \DateInterval::class, $property->getType());
        self::assertNotSame($xsdType, $property->getXsdType());
        self::assertSame('DateInterval', $property->getXsdType()->getName());
        self::assertSame('DateInterval', $property->getXsdType()->getBaseType());
    }

    #[Test]
    #[DataProvider('provideTypes')]
    public function it_can_figure_out_the_type(Property $property, string $expectedType): void
    {
        self::assertSame($expectedType, $property->getType());
    }

    public static function provideTypes()
    {
        yield 'known_simple_type' => [
            Property::fromMetaData(
                self::createTypeNamespaceMap('MyApp'),
                new EngineProperty(
                    'property',
                    XsdType::create('string')
                )
            ),
            'string'
        ];

        yield 'known_simple_base_type' => [
            Property::fromMetaData(
                self::createTypeNamespaceMap('MyApp'),
                new EngineProperty(
                    'property',
                    XsdType::create('language')
                        ->withXmlNamespace('http://www.w3.org/2001/XMLSchema')
                        ->withXmlNamespaceName('xsd')
                        ->withBaseType('string')
                        ->withMemberTypes(['token'])
                        ->withMeta(static fn (TypeMeta $meta) => $meta->withIsSimple(true))
                )
            ),
            'string'
        ];

        yield 'known_simple_base_type_with_enums' => [
            Property::fromMetaData(
                self::createTypeNamespaceMap('MyApp'),
                new EngineProperty(
                    'property',
                    XsdType::create('languageEnum')
                        ->withBaseType('string')
                        ->withMemberTypes(['token'])
                        ->withMeta(static fn (TypeMeta $meta) => $meta
                            ->withIsSimple(true)
                            ->withEnums(['EN', 'NL', 'FR'])
                        )
                )
            ),
            '\MyApp\LanguageEnum'
        ];

        yield 'root namespace' => [
            Property::fromMetaData(
                self::createTypeNamespaceMap(''),
                new EngineProperty(
                    'property',
                    XsdType::create('MyType')
                )
            ),
            '\\MyType'
        ];

        yield 'namespaced' => [
            Property::fromMetaData(
                self::createTypeNamespaceMap('MyApp'),
                new EngineProperty(
                    'property',
                    XsdType::create('MyType')
                )
            ),
            '\\MyApp\\MyType'
        ];
    }
}
