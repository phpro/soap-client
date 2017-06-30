<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;

/**
 * Class Client
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class Client
{

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $name;

    /**
     * TypeModel constructor.
     *
     * @param string $name
     * @param string $namespace
     * @param MethodMap $methods
     * @internal param string $xsdName
     * @internal param Property[] $properties
     */
    public function __construct($name, $namespace, MethodMap $methods)
    {
        $this->name = $name;
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        $this->methodMap = $methods;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return MethodMap
     */
    public function getMethodMap()
    {
        return $this->methodMap;
    }
}
