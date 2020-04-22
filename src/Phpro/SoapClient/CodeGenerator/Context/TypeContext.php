<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\Type;
use Laminas\Code\Generator\ClassGenerator;

/**
 * Class TypeContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class TypeContext implements ContextInterface
{
    /**
     * @var ClassGenerator
     */
    private $class;

    /**
     * @var Type
     */
    private $type;

    /**
     * PropertyContext constructor.
     *
     * @param ClassGenerator $class
     * @param Type           $type
     */
    public function __construct(ClassGenerator $class, Type $type)
    {
        $this->class = $class;
        $this->type = $type;
    }

    /**
     * @return ClassGenerator
     */
    public function getClass(): ClassGenerator
    {
        return $this->class;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }
}
