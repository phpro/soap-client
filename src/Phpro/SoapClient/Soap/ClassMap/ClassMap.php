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
 * Class ClassMap
 *
 * @package Phpro\SoapClient\Soap\ClassMap
 */
class ClassMap implements ClassMapInterface
{

    /**
     * @var string
     */
    private $wsdlType;

    /**
     * @var string
     */
    private $phpClassName;

    /**
     * @param $wsdlType
     * @param $phpClassName
     */
    public function __construct($wsdlType, $phpClassName)
    {
        $this->wsdlType = $wsdlType;
        $this->phpClassName = $phpClassName;
    }

    /**
     * @return string
     */
    public function getPhpClassName()
    {
        return $this->phpClassName;
    }

    /**
     * @return string
     */
    public function getWsdlType()
    {
        return $this->wsdlType;
    }
}
