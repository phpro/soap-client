<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Zend\Code\Generator\FileGenerator;

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
     * TypeContext constructor.
     *
     * @param FileGenerator $file
     * @param TypeMap       $typeMap
     */
    public function __construct(FileGenerator $file, TypeMap $typeMap)
    {
        $this->file = $file;
        $this->typeMap = $typeMap;
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
}
