<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Type;

/**
 * Class MultiArgumentRequest
 *
 * @package Phpro\SoapClient\Type
 */
class MultiArgumentRequest implements MultiArgumentRequestInterface
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * MultiArgumentRequest constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
