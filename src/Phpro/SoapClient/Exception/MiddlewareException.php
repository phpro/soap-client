<?php

namespace Phpro\SoapClient\Exception;

use Http\Client\Exception;

class MiddlewareException extends RuntimeException
{
    public static function fromHttPlugException(Exception $exception): self
    {
        return new self($exception->getMessage(), $exception->getCode(), $exception);
    }
}
