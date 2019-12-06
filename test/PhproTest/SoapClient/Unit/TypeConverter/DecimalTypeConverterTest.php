<?php

namespace PhproTest\SoapClient\Unit\TypeConverter;

use Phpro\SoapClient\Soap\TypeConverter\DecimalTypeConverter;
use PHPUnit\Framework\TestCase;

/**
 * Test Decimal TypeConverter.
 *
 * @package PhproTest\SoapClient\Unit\TypeConverter
 */
class DecimalTypeConverterTest extends TestCase
{
    /**
     * @var DecimalTypeConverter
     */
    protected $converter;

    protected function setUp(): void
    {
        $this->converter = new DecimalTypeConverter();
    }

    /**
     * @group  decimaltypeconverter
     * @covers DecimalTypeConverter::getTypeNamespace
     */
    public function testNamespaceIsSpecificValue()
    {
        $this->assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    /**
     * @group  decimaltypeconverter
     * @covers DecimalTypeConverter::getTypeName
     */
    public function testNameIsSpecificValue()
    {
        $this->assertSame('decimal', $this->converter->getTypeName());
    }

    /**
     * @group  decimaltypeconverter
     * @covers DecimalTypeConverter::convertXmlToPhp
     */
    public function testConvertXmlToPhp()
    {
        $xml = '<decimal>24.700</decimal>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertIsFloat($php);
    }

    /**
     * @group  decimaltypeconverter
     * @covers DecimalTypeConverter::convertXmlToPhp
     */
    public function testConvertXmlToPhpWhenNoTextContent()
    {
        $xml = '<decimal/>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertNull($php);
    }

    /**
     * @group  decimaltypeconverter
     * @uses   DecimalTypeConverter::getTypeName
     * @covers DecimalTypeConverter::convertPhpToXml
     */
    public function testConvertPhpToXml()
    {
        $xml = '<decimal>24.7</decimal>';

        $output = $this->converter->convertPhpToXml((float) 24.700);

        $this->assertSame($xml, $output);
    }
}
