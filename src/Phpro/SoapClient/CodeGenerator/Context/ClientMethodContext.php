<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Laminas\Code\Generator\ClassGenerator;

final readonly class ClientMethodContext implements ContextInterface
{
    public function __construct(
        private ClassGenerator $class,
        private ClientMethod $method
    ) {
    }

    public function getClass(): ClassGenerator
    {
        return $this->class;
    }

    public function getMethod(): ClientMethod
    {
        return $this->method;
    }
}
