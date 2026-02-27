<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\Type;
use Laminas\Code\Generator\ClassGenerator;

final readonly class TypeContext implements ContextInterface
{
    public function __construct(
        private ClassGenerator $class,
        private Type $type
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
}
