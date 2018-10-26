<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Exception;

class MetadataException extends RuntimeException
{
    public static function typeNotFound(string $name): self
    {
        return new self('No SOAP type find with name '.$name.'.');
    }
}
