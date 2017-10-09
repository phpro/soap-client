<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Soap\TypeConverter;

use DateTime;
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
    public function getTypeNamespace()
    {
        return 'http://www.w3.org/2001/XMLSchema';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return 'dateTime';
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

        $dateTime = new DateTime($doc->textContent);
        $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

        return $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function convertPhpToXml($php)
    {
        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php->format('Y-m-d\TH:i:sP'));
    }
}
