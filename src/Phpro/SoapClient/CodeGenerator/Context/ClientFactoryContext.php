<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

final readonly class ClientFactoryContext implements ContextInterface
{
    public function __construct(
        private ClientContext $clientContext,
        private ClassMapContext $classMapContext
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getClientName(): string
    {
        return $this->clientContext->getName();
    }

    /**
     * @return non-empty-string
     */
    public function getClientNamespace(): string
    {
        return $this->clientContext->getNamespace();
    }

    /**
     * @return non-empty-string
     */
    public function getClassmapName(): string
    {
        return $this->classMapContext->getName();
    }

    /**
     * @return non-empty-string
     */
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
