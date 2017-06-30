<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\Type;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class ClientContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class ClientContext implements ContextInterface
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
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }
}
