<?php

namespace Phpro\SoapClient\Exception;

/**
 * Class SoapException
 *
 * @package Phpro\SoapClient\Exception
 */
class SoapException extends RuntimeException
{
    /**
     * @param \Throwable $throwable
     *
     * @return SoapException
     */
    public static function fromThrowable(\Throwable $throwable): self
    {
        return new self($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
    }
}
