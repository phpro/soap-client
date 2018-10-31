<?php

namespace Phpro\SoapClient\Exception;

/**
 * Class SoapException
 *
 * @package Phpro\SoapClient\Exception
 */
class SoapException extends RuntimeException
{
    public function __construct(string $message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, (int)$code, $previous);

        if (!is_int($code)) {
            $this->code = $code;
        }
    }

    /**
     * @param \Throwable $throwable
     *
     * @return SoapException
     */
    public static function fromThrowable(\Throwable $throwable): self
    {
        return new self($throwable->getMessage(), $throwable->getCode(), $throwable);
    }
}
