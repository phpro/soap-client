<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class ClassMapContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class ClassMapContext implements ContextInterface
{
    /**
     * @var FileGenerator
     */
    private $file;

    /**
     * @var TypeMap
     */
    private $typeMap;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $namespace;

    /**
     * TypeContext constructor.
     *
     * @param FileGenerator $file
     * @param TypeMap       $typeMap
     * @param string        $name
     * @param string        $namespace
     */
    public function __construct(FileGenerator $file, TypeMap $typeMap, string $name, string $namespace)
    {
        $this->file = $file;
        $this->typeMap = $typeMap;
        $this->name = $name;
        $this->namespace = $namespace;
    }

    /**
     * @return FileGenerator
     */
    public function getFile(): FileGenerator
    {
        return $this->file;
    }

    /**
     * @return TypeMap
     */
    public function getTypeMap(): TypeMap
    {
        return $this->typeMap;
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
     * @return string
     */
    public function getFqcn(): string
    {
        return $this->namespace.'\\'.$this->name;
    }
}
