<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\CodeGenerator\TypeEnhancer\Calculator;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\TypeNameCalculator;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

class TypeNameCalculatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideTypeNameCalculations
     */
    public function it_can_calculate_type_name(XsdType $xsdType, string $expected): void
    {
        $calculate = new TypeNameCalculator();
        self::assertSame($calculate($xsdType), $expected);

    }

    public function provideTypeNameCalculations()
    {
        yield 'complexType' => [
            XsdType::create('ComplexType')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(false)
            ),
            'ComplexType'
        ];
        yield 'known-type' => [
            XsdType::create('string')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(true)
            ),
            'string'
        ];
        yield 'base-type' => [
            XsdType::create('StringLength1To128')
                ->withBaseType('string')
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(true)
                ),
            'string'
        ];
        yield 'unknown-base-type' => [
            XsdType::create('StringLength1To128')
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(true)
                ),
            'StringLength1To128'
        ];
        yield 'list-known-type' => [
            XsdType::create('string')
                ->withMemberTypes([])
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withIsSimple(true)
                        ->withIsList(true)
                ),
            'string'
        ];
        yield 'list-member-type' => [
            XsdType::create('StringLength1To128')
                ->withMemberTypes(['string'])
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withIsSimple(true)
                        ->withIsList(true)
                ),
            'string'
        ];
        yield 'list-unknown-type' => [
            XsdType::create('SomeSimple')
                ->withBaseType('SomeOtherSimple')
                ->withMemberTypes([])
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withIsSimple(true)
                        ->withIsList(true)
                ),
            'mixed'
        ];
    }
}
