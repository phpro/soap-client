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
    public function getTypeNamespace();

    /**
     * Get type name.
     *
     * @return string
     */
    public function getTypeName();

    /**
     * Convert given XML string to PHP type.
     *
     * @param string $xml XML string
     *
     * @return mixed
     */
    public function convertXmlToPhp($xml);

    /**
     * Convert PHP type to XML string.
     *
     * @param mixed $php PHP type
     *
     * @return string
     */
    public function convertPhpToXml($php);
}
