<?php

namespace Phpro\SoapClient\Soap\TypeConverter;

/**
 * Interface TypeConverterInterface
 *
 * A type converter converts between SOAP and PHP types
 *
 * @package Phpro\SoapClient\Soap\TypeConverter
 */
interface TypeConverterInterface
{
    /**
     * Get type namespace.
     *
     * @return string
     */
    public function getTypeNamespace(): string;

    /**
     * Get type name.
     *
     * @return string
     */
    public function getTypeName(): string;

    /**
     * Convert given XML string to PHP type.
     *
     * @param string $xml XML string
     *
     * @return mixed
     */
    public function convertXmlToPhp(string $xml);

    /**
     * Convert PHP type to XML string.
     *
     * @param mixed $php PHP type
     *
     * @return string
     */
    public function convertPhpToXml($php): string;
}
