<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class ClientMethodContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class ClientMethodContext implements ContextInterface
{
    /**
     * @var ClassGenerator
     */
    private $class;

    /**
     * @var ClientMethod
     */
    private $method;

    /**
     * PropertyContext constructor.
     *
     * @param ClassGenerator $class
     * @param ClientMethod $method
     */
    public function __construct(ClassGenerator $class, ClientMethod $method)
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
     * @return ClientMethod
     */
    public function getMethod()
    {
        return $this->method;
    }
}
