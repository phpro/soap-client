<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;

final readonly class ClientContext implements ContextInterface
{
    public function __construct(
        private ClassGenerator $class,
        private ClientConfig $clientConfig
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->clientConfig->name;
    }

    /**
     * @return non-empty-string
     */
    public function getNamespace(): string
    {
        return $this->clientConfig->destination->namespace;
    }

    /**
     * @return non-empty-string
     */
    public function getFqcn(): string
    {
        return $this->clientConfig->fqcn();
    }

    public function getClass(): ClassGenerator
    {
        return $this->class;
    }
}
