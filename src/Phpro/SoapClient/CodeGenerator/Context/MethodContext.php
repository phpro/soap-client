<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\Method;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class MethodContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class MethodContext implements ContextInterface
{
    /**
     * @var ClassGenerator
     */
    private $class;

    /**
     * @var Method
     */
    private $method;

    /**
     * PropertyContext constructor.
     *
     * @param ClassGenerator $class
     * @param Method $method
     */
    public function __construct(ClassGenerator $class, Method $method)
    {
        $this->class = $class;
        $this->method = $method;
    }

    /**
     * @return ClassGenerator
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return Method
     */
    public function getMethod()
    {
        return $this->method;
    }
}
