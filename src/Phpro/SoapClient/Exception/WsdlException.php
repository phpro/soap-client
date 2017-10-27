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
 * Class WsdlException
 *
 * @package Phpro\SoapClient\Exception
 */
class WsdlException extends RuntimeException
{
    /**
     * @param $path
     *
     * @return WsdlException
     */
    public static function notFound($path)
    {
        return new self(
            sprintf('The WSDL could not be loaded from location: %s', $path)
        );
    }

    /**
     * @param \Throwable $exception
     *
     * @return WsdlException
     */
    public static function fromException(\Throwable $exception)
    {
        return new self(
            $exception->getMessage(),
            $exception->getCode(),
            $exception
        );
    }
}
