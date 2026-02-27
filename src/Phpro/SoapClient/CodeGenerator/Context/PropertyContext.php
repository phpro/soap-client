<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Laminas\Code\Generator\ClassGenerator;

final readonly class PropertyContext implements ContextInterface
{
    public function __construct(
        private ClassGenerator $class,
        private Type $type,
        private Property $property,
        private CodeGeneratorContext $codeGeneratorContext,
    ) {
    }

    public function getClass(): ClassGenerator
    {
        return $this->class;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getCodeGeneratorContext(): CodeGeneratorContext
    {
        return $this->codeGeneratorContext;
    }
}
