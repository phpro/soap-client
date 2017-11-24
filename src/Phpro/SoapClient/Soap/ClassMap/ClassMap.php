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
     * @param string $wsdlType
     * @param string $phpClassName
     */
    public function __construct(string $wsdlType, string $phpClassName)
    {
        $this->wsdlType = $wsdlType;
        $this->phpClassName = $phpClassName;
    }

    /**
     * @return string
     */
    public function getPhpClassName(): string
    {
        return $this->phpClassName;
    }

    /**
     * @return string|null
     */
    public function getWsdlType()
    {
        return $this->wsdlType;
    }
}
