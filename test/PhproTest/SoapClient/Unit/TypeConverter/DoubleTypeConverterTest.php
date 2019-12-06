<?php

namespace PhproTest\SoapClient\Unit\TypeConverter;

use Phpro\SoapClient\Soap\TypeConverter\DoubleTypeConverter;
use PHPUnit\Framework\TestCase;

/**
 * Test Double TypeConverter.
 *
 * @package PhproTest\SoapClient\Unit\TypeConverter
 */
class DoubleTypeConverterTest extends TestCase
{
    /**
     * @var DoubleTypeConverter
     */
    protected $converter;

    protected function setUp(): void
    {
        $this->converter = new DoubleTypeConverter();
    }

    /**
     * @group  doubletypeconverter
     * @covers DoubleTypeConverter::getTypeNamespace
     */
    public function testNamespaceIsSpecificValue()
    {
        $this->assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    /**
     * @group  doubletypeconverter
     * @covers DoubleTypeConverter::getTypeName
     */
    public function testNameIsSpecificValue()
    {
        $this->assertSame('double', $this->converter->getTypeName());
    }

    /**
     * @group  doubletypeconverter
     * @covers DoubleTypeConverter::convertXmlToPhp
     */
    public function testConvertXmlToPhp()
    {
        $xml = '<double>24.700</double>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertIsFloat($php);
    }

    /**
     * @group  doubletypeconverter
     * @covers DoubleTypeConverter::convertXmlToPhp
     */
    public function testConvertXmlToPhpWhenNoTextContent()
    {
        $xml = '<double/>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertNull($php);
    }

    /**
     * @group  doubletypeconverter
     * @uses   DoubleTypeConverter::getTypeName
     * @covers DoubleTypeConverter::convertPhpToXml
     */
    public function testConvertPhpToXml()
    {
        $xml = '<double>24.7</double>';

        $output = $this->converter->convertPhpToXml((float) 24.700);

        $this->assertSame($xml, $output);
    }
}
