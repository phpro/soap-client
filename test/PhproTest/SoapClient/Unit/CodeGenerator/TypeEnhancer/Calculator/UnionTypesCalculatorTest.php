<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\CodeGenerator\TypeEnhancer\Calculator;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\UnionTypesCalculator;
use PHPUnit\Framework\TestCase;
use Psl\Type\Exception\AssertException;
use Soap\Engine\Metadata\Model\TypeMeta;

class UnionTypesCalculatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExpectations
     */
    public function it_can_enhance_types(
        TypeMeta $meta,
        string $expected,
    ): void{
        $calculator = new UnionTypesCalculator();

        self::assertSame($expected, $calculator($meta));
    }

    /** @test */
    public function it_fails_on_empty_enumerations(): void
    {
        $this->expectException(AssertException::class);

        (new UnionTypesCalculator())(new TypeMeta());
    }

    public function provideExpectations()
    {
        yield 'single' => [
            (new TypeMeta())->withUnions([
                ['type' => 'string', 'isList' => false, 'namespace' => 'xx'],
            ]),
            "string",
        ];
        yield 'multi' => [
            (new TypeMeta())->withUnions([
                ['type' => 'string', 'isList' => false, 'namespace' => 'xx'],
                ['type' => 'int', 'isList' => false, 'namespace' => 'xx'],
            ]),
            "string | int",
        ];
        yield 'list' => [
            (new TypeMeta())->withUnions([
                ['type' => 'string', 'isList' => false, 'namespace' => 'xx'],
                ['type' => 'int', 'isList' => true, 'namespace' => 'xx'],
            ]),
            "string | list<int>",
        ];
        yield 'complexType' => [
            (new TypeMeta())->withUnions([
                ['type' => 'My_Type', 'isList' => false, 'namespace' => 'xx'],
                ['type' => 'Your_Type', 'isList' => true, 'namespace' => 'xx'],
            ]),
            "mixed | list<mixed>",
        ];
    }
}
