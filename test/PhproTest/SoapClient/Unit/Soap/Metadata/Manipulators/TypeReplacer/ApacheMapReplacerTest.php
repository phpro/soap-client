<?php
declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Metadata\Manipulators\TypeReplacer;

use Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer\ApacheMapReplacer;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Detector\ApacheMapDetector;

final class ApacheMapReplacerTest extends TestCase
{
    #[DataProvider('provideTestCases')]
    #[Test]
    public function it_can_replace_apache_map(XsdType $in, XsdType $expected): void
    {
        self::assertEquals($expected, (new ApacheMapReplacer())($in));
    }

    public static function provideTestCases()
    {
        yield 'map' => [
            $baseType = (new XsdType(''))
                ->withXmlNamespace(ApacheMapDetector::NAMESPACE_URI)
                ->withXmlTypeName('Map'),
            $baseType->copy('array')
                ->withBaseType('array')
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(true)
                )
        ];
        yield 'nomap' => [
            $baseType = (new XsdType(''))
                ->withXmlNamespace('http://none')
                ->withXmlTypeName('Map'),
            $baseType
        ];
    }
}
