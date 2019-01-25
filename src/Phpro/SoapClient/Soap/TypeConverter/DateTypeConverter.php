<?php

namespace Phpro\SoapClient\Soap\TypeConverter;

use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;

/**
 * Class DateTypeConverter
 *
 * Converts between PHP \DateTime and SOAP date objects
 *
 * @package Phpro\SoapClient\Soap\TypeConverter
 */
class DateTypeConverter implements TypeConverterInterface
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
        return 'date';
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

        return new DateTimeImmutable($doc->textContent);
    }

    /**
     * {@inheritdoc}
     */
    public function convertPhpToXml($php): string
    {
        if (!$php instanceof DateTimeInterface) {
            return '';
        }

        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php->format('Y-m-d'));
    }
}
