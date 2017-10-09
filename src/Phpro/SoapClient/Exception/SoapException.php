<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public static function fromThrowable(\Throwable $throwable)
    {
        return new self($throwable->getMessage(), $throwable->getCode(), $throwable);
    }
}
