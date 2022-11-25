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
     * @var ClientMethodMap
     */
    private $methodMap;

    /**
     * @var non-empty-string
     */
    private $namespace;

    /**
     * @var non-empty-string
     */
    private $name;

    /**
     * TypeModel constructor.
     *
     * @param non-empty-string $name
     * @param non-empty-string $namespace
     * @param ClientMethodMap $methods
     * @internal param string $xsdName
     * @internal param Property[] $properties
     */
    public function __construct(string $name, string $namespace, ClientMethodMap $methods)
    {
        $this->name = $name;
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        $this->methodMap = $methods;
    }

    /**
     * @return non-empty-string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return non-empty-string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ClientMethodMap
     */
    public function getMethodMap()
    {
        return $this->methodMap;
    }
}
