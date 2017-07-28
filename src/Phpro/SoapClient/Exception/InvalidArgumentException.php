<?php

namespace Phpro\SoapClient\Exception;

/**
 * Class InvalidArgumentException
 *
 * @package Phpro\SoapClient\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * @return InvalidArgumentException
     */
    public static function wsdlConfigurationIsMissing()
    {
        return new static('You did not configure a WSDL file.');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function destinationConfigurationIsMissing()
    {
        return new static('You did not configure a destination.');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function invalidConfigFile()
    {
        return new static('You have to provide a code-generator config file which returns a ConfigInterface.');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function clientNamespaceIsMissing()
    {
        return new static('You did not configure a client namespace.');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function typeNamespaceIsMissing()
    {
        return new static('You did not configure a type namespace.');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function clientDestinationIsMissing()
    {
        return new static('You did not configure a client destination.');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function typeDestinationIsMissing()
    {
        return new static('You did not configure a type destination.');
    }
}
