<?php

namespace Phpro\SoapClient\Soap\TypeConverter;

use DOMDocument;

/**
 * Class DoubleTypeConverter
 *
 * Convert between PHP float and Soap double objects
 *
 * @package Phpro\SoapClient\Soap\TypeConverter
 */
class DoubleTypeConverter implements TypeConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTypeNamespace(): string
    {
        return 'http://www.w3.org/2001/XMLSchema';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName(): string
    {
        return 'double';
    }

    /**
     * {@inheritdoc}
     */
    public function convertXmlToPhp(string $data)
    {
        $doc = new DOMDocument();
        $doc->loadXML($data);

        if ('' === $doc->textContent) {
            return null;
        }

        return (float) $doc->textContent;
    }

    /**
     * {@inheritdoc}
     */
    public function convertPhpToXml($php): string
    {
        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php);
    }
}
