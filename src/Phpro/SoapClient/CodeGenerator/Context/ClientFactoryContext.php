<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

class ClientFactoryContext implements ContextInterface
{
    /**
     * @var ClassMapContext
     */
    private $classMapContext;

    /**
     * @var ClientContext
     */
    private $clientContext;

    public function __construct(
        ClientContext $clientContext,
        ClassMapContext $classMapContext
    ) {
        $this->classMapContext = $classMapContext;
        $this->clientContext = $clientContext;
    }

    public function getClientName(): string
    {
        return $this->clientContext->getName();
    }

    public function getClientNamespace(): string
    {
        return $this->clientContext->getNamespace();
    }

    public function getClassmapName(): string
    {
        return $this->classMapContext->getName();
    }

    public function getClassmapNamespace(): string
    {
        return $this->classMapContext->getNamespace();
    }

    /**
     * @return non-empty-string
     */
    public function getClientFqcn(): string
    {
        return $this->clientContext->getFqcn();
    }

    /**
     * @return non-empty-string
     */
    public function getClassmapFqcn(): string
    {
        return $this->classMapContext->getFqcn();
    }
}
