<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Laminas\Code\Generator\FileGenerator;

final readonly class ClassMapContext implements ContextInterface
{
    public function __construct(
        private FileGenerator $file,
        private TypeMap $typeMap,
        private ClassMapConfig $classMap
    ) {
    }

    public function getFile(): FileGenerator
    {
        return $this->file;
    }

    public function getTypeMap(): TypeMap
    {
        return $this->typeMap;
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->classMap->name;
    }

    /**
     * @return non-empty-string
     */
    public function getNamespace(): string
    {
        return $this->classMap->destination->namespace;
    }

    /**
     * @return non-empty-string
     */
    public function getFqcn(): string
    {
        return $this->classMap->fqcn();
    }
}
