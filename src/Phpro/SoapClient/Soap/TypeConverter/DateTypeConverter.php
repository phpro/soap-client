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
    public function getTypeNamespace()
    {
        return 'http://www.w3.org/2001/XMLSchema';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return 'date';
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

        return $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function convertPhpToXml($php)
    {
        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php->format('Y-m-d'));
    }
}
