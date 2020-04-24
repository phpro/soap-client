<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Laminas\Code\Generator\ClassGenerator;

/**
 * Class PropertyContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class PropertyContext implements ContextInterface
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
     * @var Property
     */
    private $property;

    /**
     * PropertyContext constructor.
     *
     * @param ClassGenerator $class
     * @param Type           $type
     * @param Property       $property
     */
    public function __construct(ClassGenerator $class, Type $type, Property $property)
    {
        $this->class = $class;
        $this->type = $type;
        $this->property = $property;
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

    /**
     * @return Property
     */
    public function getProperty(): Property
    {
        return $this->property;
    }
}
