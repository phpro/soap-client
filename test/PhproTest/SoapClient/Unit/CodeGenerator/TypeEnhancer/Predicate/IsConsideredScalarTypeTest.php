<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\CodeGenerator\TypeEnhancer\Predicate;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Predicate\IsConsideredScalarType;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\TypeMeta;

class IsConsideredScalarTypeTest extends TestCase
{
    /**
     * @dataProvider provideTests
     * @test
     */
    public function it_knows_if_a_type_is_considered_scalar(TypeMeta $meta, bool $expected): void
    {
        self::assertSame($expected, (new IsConsideredScalarType())($meta));
    }

    public function provideTests()
    {
        yield 'not' => [
            (new TypeMeta()),
            false,
        ];
        yield 'simple' => [
            (new TypeMeta())->withIsSimple(true),
            true,
        ];
        yield 'attribute' => [
            (new TypeMeta())->withIsSimple(true),
            true,
        ];
    }
}
