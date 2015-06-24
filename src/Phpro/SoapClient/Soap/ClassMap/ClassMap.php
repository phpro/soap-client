<?php

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
