<?php


namespace Phpro\SoapClient\Exception;

/**
 * Class AssemblerException
 *
 * @package Phpro\SoapClient\Exception
 */
class AssemblerException extends RunTimeException
{
    /**
     * @param \Exception $e
     *
     * @return AssemblerException
     */
    public static function fromException(\Exception $e)
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}
