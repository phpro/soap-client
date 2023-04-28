<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\CodeGenerator\TypeEnhancer;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\MetaTypeEnhancer;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\TypeMeta;

class MetaTypeEnhancerTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExpectations
     */
    public function it_can_enhance_types(
        TypeMeta $meta,
        string $type,
        string $expectedDocBlock,
        string $expectedPhp,
    ): void{
        $enhancer = new MetaTypeEnhancer($meta);

        self::assertSame($expectedDocBlock, $enhancer->asDocBlockType($type));
        self::assertSame($expectedPhp, $enhancer->asPhpType($type));
    }

    public function provideExpectations()
    {
        yield 'simpleType' => [
            new TypeMeta(),
            'simple',
            'simple',
            'simple',
        ];
        yield 'array' => [
            (new TypeMeta())->withIsList(true),
            'simple',
            'array<int<min,max>, simple>',
            'array',
        ];
        yield 'min' => [
            (new TypeMeta())->withIsList(true)->withMinOccurs(1),
            'simple',
            'array<int<1,max>, simple>',
            'array',
        ];
        yield 'max' => [
            (new TypeMeta())->withIsList(true)->withMaxOccurs(3),
            'simple',
            'array<int<min,3>, simple>',
            'array',
        ];
        yield 'nullable' => [
            (new TypeMeta())->withIsNullable(true),
            'simple',
            'null|simple',
            '?simple',
        ];
        yield 'nullable-array' => [
            (new TypeMeta())->withIsList(true)->withIsNullable(true),
            'simple',
            'null|array<int<min,max>, simple>',
            '?array',
        ];
    }
}
