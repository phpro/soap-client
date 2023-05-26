<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\CodeGenerator\TypeEnhancer\Calculator;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\EnumValuesCalculator;
use PHPUnit\Framework\TestCase;
use Psl\Type\Exception\AssertException;
use Soap\Engine\Metadata\Model\TypeMeta;

class EnumValuesCalculatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExpectations
     */
    public function it_can_enhance_types(
        TypeMeta $meta,
        string $expected,
    ): void{
        $calculator = new EnumValuesCalculator();

        self::assertSame($expected, $calculator($meta));
    }

    /** @test */
    public function it_fails_on_empty_enumerations(): void
    {
        $this->expectException(AssertException::class);

        (new EnumValuesCalculator())(new TypeMeta());
    }

    public function provideExpectations()
    {
        yield 'single' => [
            (new TypeMeta())->withEnums(['a']),
            "'a'",
        ];
        yield 'multi' => [
            (new TypeMeta())->withEnums(['a', 'b']),
            "'a' | 'b'",
        ];
        yield 'numeric' => [
            (new TypeMeta())->withEnums(['0', '1']),
            "'0' | '1'",
        ];
        yield 'quoted' => [
            (new TypeMeta())->withEnums(['a', '\'b']),
            "'a' | '\\'b'",
        ];
        yield 'not-quoted' => [
            (new TypeMeta())->withEnums(['a', '"b']),
            "'a' | '\"b'",
        ];
    }
}
