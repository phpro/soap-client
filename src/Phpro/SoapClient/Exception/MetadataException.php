<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Exception;

class MetadataException extends RuntimeException
{
    public static function typeNotFound(string $name): self
    {
        return new self('No SOAP type found with name '.$name.'.');
    }

    public static function methodNotFound(string $name): self
    {
        return new self('No SOAP method found with name '.$name.'.');
    }
}
