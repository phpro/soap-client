<?php

namespace Phpro\SoapClient\Exception;

use Throwable;

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
     * @param Throwable $e
     * @return InvalidArgumentException
     */
    public static function wsdlCouldNotBeProvided(Throwable $e): self
    {
        return new static('The WSDL could not be loaded: ' . $e->getMessage(), 0, $e);
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
}
