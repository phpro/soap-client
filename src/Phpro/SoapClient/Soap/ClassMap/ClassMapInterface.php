<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Soap\ClassMap;

/**
 * Class ClassMapInterface
 *
 * @package Phpro\SoapClient\Soap\ClassMap
 */
interface ClassMapInterface
{

    /**
     * @return string
     */
    public function getWsdlType();

    /**
     * @return string
     */
    public function getPhpClassName();
}
