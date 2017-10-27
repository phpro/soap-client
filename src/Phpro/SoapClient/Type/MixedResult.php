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
 * Class MixedResult
 *
 * @package Phpro\SoapClient\Type
 */
class MixedResult implements ResultInterface
{
    /**
     * @var mixed
     */
    private $result;

    /**
     * MixedResult constructor.
     *
     * @param mixed $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
