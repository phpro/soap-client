<?php

namespace Phpro\SoapClient\Exception;

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

    public static function invalidConfigFile(): self
    {
        return new static('You have to provide a code-generator config file which returns a Config class instance.');
    }

    public static function clientIsMissing(): self
    {
        return new static('You did not configure a client.');
    }

    public static function typeNamespaceMapIsMissing(): self
    {
        return new static('You did not configure a namespace mapping for the types.');
    }

    public static function classmapMissing(): self
    {
        return new static('You did not configure a classmap.');
    }
}
