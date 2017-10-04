<?php

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
    public function getWsdlType(): string;

    /**
     * @return string
     */
    public function getPhpClassName(): string;
}
