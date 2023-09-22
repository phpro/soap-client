<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\CodeGenerator\TypeEnhancer\Predicate;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Predicate\IsConsideredNullableType;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\TypeMeta;

class IsConsideredNullableTypeTest extends TestCase
{
    /**
     * @dataProvider provideTests
     * @test
     */
    public function it_knows_if_a_type_is_considered_nullable(TypeMeta $meta, bool $expected): void
    {
        self::assertSame($expected, (new IsConsideredNullableType())($meta));
    }

    public function provideTests()
    {
        yield 'empty' => [
            (new TypeMeta()),
            false,
        ];
        yield 'nullable' => [
            (new TypeMeta())->withIsNullable(true),
            true,
        ];
        yield 'not-nullable' => [
            (new TypeMeta())->withIsNullable(false),
            false,
        ];
        yield 'nillable' => [
            (new TypeMeta())->withIsNil(true),
            true,
        ];
        yield 'not-nillable' => [
            (new TypeMeta())->withIsNil(false),
            false,
        ];
        yield 'default-attribute' => [
            (new TypeMeta())->withIsAttribute(true),
            true,
        ];
        yield 'optional-attribute' => [
            (new TypeMeta())->withIsAttribute(true)->withUse('optional'),
            true,
        ];
        yield 'required-attribute' => [
            (new TypeMeta())->withIsAttribute(true)->withUse('required'),
            false,
        ];
        yield 'prohibit-attribute' => [
            (new TypeMeta())->withIsAttribute(true)->withUse('prohibit'),
            false,
        ];
    }
}
