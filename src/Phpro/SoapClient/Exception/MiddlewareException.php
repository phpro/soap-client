<?php

namespace Phpro\SoapClient\Exception;

use Http\Client\Exception;
use Throwable;

class MiddlewareException extends RuntimeException
{
    public static function fromHttPlugException(Exception $exception): self
    {
        if ($exception instanceof Throwable) {
            return new self($exception->getMessage(), $exception->getCode(), $exception);
        }

        return new self('Something went wrong: '.get_class($exception));
    }
}
