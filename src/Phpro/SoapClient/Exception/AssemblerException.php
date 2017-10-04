<?php


namespace Phpro\SoapClient\Exception;

/**
 * Class AssemblerException
 *
 * @package Phpro\SoapClient\Exception
 */
class AssemblerException extends RuntimeException
{
    /**
     * @param \Exception $e
     *
     * @return AssemblerException
     */
    public static function fromException(\Exception $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}
