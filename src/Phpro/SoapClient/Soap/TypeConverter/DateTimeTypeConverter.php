<?php

namespace Phpro\SoapClient\Soap\TypeConverter;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DOMDocument;

/**
 * Class DateTimeTypeConverter
 *
 * Converts between PHP \DateTime and SOAP dateTime objects
 *
 * @package Phpro\SoapClient\Soap\TypeConverter
 */
class DateTimeTypeConverter implements TypeConverterInterface
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
        return 'dateTime';
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

        $dateTime = new DateTimeImmutable($doc->textContent);

        return $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
    }

    /**
     * {@inheritdoc}
     */
    public function convertPhpToXml($php): string
    {
        if (!$php instanceof DateTimeInterface) {
            return '';
        }

        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php->format('Y-m-d\TH:i:sP'));
    }
}
