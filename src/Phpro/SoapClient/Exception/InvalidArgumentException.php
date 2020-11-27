<?php

namespace Phpro\SoapClient\Exception;

use Throwable;

/**
 * Class InvalidArgumentException
 *
 * @package Phpro\SoapClient\Exception
 */
final class InvalidArgumentException extends \InvalidArgumentException
{
    public static function engineNotConfigured(): self
    {
        return new static('You did not configure a soap engine');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function destinationConfigurationIsMissing(): self
    {
        return new static('You did not configure a destination.');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function invalidConfigFile(): self
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

    /**
     * @return InvalidArgumentException
     */
    public static function classmapNameMissing()
    {
        return new static('You did not configure a classmap name.');
    }

    /**
     * @return InvalidArgumentException
     */
    public static function classmapNamespaceMissing()
    {
        return new static('You did not configure a classmap namespace.');
    }
    /**
     * @return InvalidArgumentException
     */
    public static function classmapDestinationMissing()
    {
        return new static('You did not configure a classmap destination.');
    }
}
