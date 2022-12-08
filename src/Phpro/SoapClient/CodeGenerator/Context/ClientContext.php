<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Laminas\Code\Generator\ClassGenerator;

/**
 * Class ClientContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class ClientContext implements ContextInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var ClassGenerator
     */
    private $class;

    /**
     * PropertyContext constructor.
     *
     * @param string $name
     * @param string $namespace
     */
    public function __construct(ClassGenerator $class, string $name, string $namespace)
    {
        $this->class = $class;
        $this->name = $name;
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return non-empty-string
     */
    public function getFqcn(): string
    {
        return $this->namespace.'\\'.$this->name;
    }

    public function getClass(): ClassGenerator
    {
        return $this->class;
    }
}
