<?php

namespace Phpro\SoapClient\Soap\TypeConverter;

use DOMDocument;

/**
 * Class DecimalTypeConverter
 *
 * Convert between PHP float and Soap decimal objects
 *
 * @package Phpro\SoapClient\Soap\TypeConverter
 */
class DecimalTypeConverter implements TypeConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTypeNamespace()
    {
        return 'http://www.w3.org/2001/XMLSchema';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return 'decimal';
    }

    /**
     * {@inheritdoc}
     */
    public function convertXmlToPhp($data)
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
    public function convertPhpToXml($php)
    {
        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php);
    }
}