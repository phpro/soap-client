<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Util;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NormalizerTest extends TestCase
{
    /**
     * @return iterable<string, array{string, ?string}>
     */
    public static function provideNamespaceSegmentCases(): iterable
    {
        yield 'normal prefix' => ['gml', 'Gml'];
        yield 'hyphenated' => ['my-prefix', 'MyPrefix'];
        yield 'leading digit' => ['2way', 'Ns2way'];
        yield 'reserved word' => ['list', 'ListType'];
        yield 'empty string' => ['', null];
        yield 'purely numeric' => ['123', 'Ns123'];
        yield 'dotted' => ['my.prefix', 'MyPrefix'];
        yield 'uppercase' => ['GML', 'GML'];
    }

    #[Test]
    #[DataProvider('provideNamespaceSegmentCases')]
    public function it_normalizes_namespace_segment(string $input, ?string $expected): void
    {
        $this->assertSame($expected, Normalizer::normalizeNamespaceSegment($input));
    }
}
