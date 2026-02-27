<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\CodingStandards;

use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;
use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DefaultCodingStandardsStrategyTest extends TestCase
{
    private DefaultCodingStandardsStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new DefaultCodingStandardsStrategy();
    }

    #[Test]
    public function it_implements_coding_standards_strategy_interface(): void
    {
        $this->assertInstanceOf(CodingStandardsStrategyInterface::class, $this->strategy);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideTypeNameCases(): iterable
    {
        yield 'simple' => ['MyType', 'MyType'];
        yield 'snake_case' => ['my_type', 'MyType'];
        yield 'hyphenated' => ['my-type', 'MyType'];
        yield 'reserved keyword' => ['list', 'ListType'];
    }

    #[Test]
    #[DataProvider('provideTypeNameCases')]
    public function it_normalizes_type_name(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->strategy->normalizeTypeName($input));
        $this->assertSame(Normalizer::normalizeClassname($input), $this->strategy->normalizeTypeName($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideOperationNameCases(): iterable
    {
        yield 'PascalCase' => ['GetUsers', 'getUsers'];
        yield 'camelCase' => ['getUsers', 'getUsers'];
        yield 'snake_case' => ['get_users', 'get_users'];
        yield 'reserved keyword' => ['list', 'listCall'];
    }

    #[Test]
    #[DataProvider('provideOperationNameCases')]
    public function it_normalizes_operation_name(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->strategy->normalizeOperationName($input));
        $this->assertSame(Normalizer::normalizeMethodName($input), $this->strategy->normalizeOperationName($input));
    }

    /**
     * @return iterable<string, array{string, ?string}>
     */
    public static function provideNamespaceSegmentCases(): iterable
    {
        yield 'normal prefix' => ['gml', 'Gml'];
        yield 'hyphenated' => ['my-prefix', 'MyPrefix'];
        yield 'leading digit' => ['2way', 'Ns2way'];
        yield 'empty string' => ['', null];
    }

    #[Test]
    #[DataProvider('provideNamespaceSegmentCases')]
    public function it_normalizes_namespace_segment(string $input, ?string $expected): void
    {
        $this->assertSame($expected, $this->strategy->normalizeNamespaceSegment($input));
        $this->assertSame(
            Normalizer::normalizeNamespaceSegment($input),
            $this->strategy->normalizeNamespaceSegment($input)
        );
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideEnumCaseNameCases(): iterable
    {
        yield 'simple' => ['Active', 'Active'];
        yield 'hyphenated' => ['some-value', 'someValue'];
        yield 'empty' => ['', 'Empty'];
        yield 'numeric' => ['42', 'Value_42'];
    }

    #[Test]
    #[DataProvider('provideEnumCaseNameCases')]
    public function it_normalizes_enum_case_name(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->strategy->normalizeEnumCaseName($input));
        $this->assertSame(Normalizer::normalizeEnumCaseName($input), $this->strategy->normalizeEnumCaseName($input));
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function providePropertyAccessorMethodNameCases(): iterable
    {
        yield 'getter' => ['get', 'firstName', 'getFirstName'];
        yield 'setter' => ['set', 'firstName', 'setFirstName'];
        yield 'with prefix' => ['with', 'firstName', 'withFirstName'];
    }

    #[Test]
    #[DataProvider('providePropertyAccessorMethodNameCases')]
    public function it_generates_property_accessor_method_name(
        string $prefix,
        string $property,
        string $expected
    ): void {
        $this->assertSame($expected, $this->strategy->generatePropertyAccessorMethodName($prefix, $property));
        $this->assertSame(
            Normalizer::generatePropertyMethod($prefix, $property),
            $this->strategy->generatePropertyAccessorMethodName($prefix, $property)
        );
    }

    #[Test]
    public function it_returns_property_name_as_parameter_name(): void
    {
        $this->assertSame('firstName', $this->strategy->normalizeParameterName('firstName'));
        $this->assertSame('some_prop', $this->strategy->normalizeParameterName('some_prop'));
    }
}
