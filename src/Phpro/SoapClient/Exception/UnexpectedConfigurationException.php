<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Exception;

class UnexpectedConfigurationException extends RuntimeException
{
    public static function expectedTypeButGot(string $configurationKey, string $expectedType, $value): self
    {
        throw new self(
            sprintf(
                'Invalid configuration. Expected value of option %s to be of type %s but got %s.',
                $configurationKey,
                $expectedType,
                gettype($value)
            )
        );
    }
}
