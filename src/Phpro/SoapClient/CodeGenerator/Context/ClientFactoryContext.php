<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\DuplicateType;

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

    /**
     * @var string
     */
    private $typeNamespace;

    /**
     * @var DuplicateType[]|array
     */
    private $duplicateTypes;

    public function __construct(
        ClientContext $clientContext,
        ClassMapContext $classMapContext,
        string $typeNamespace,
        array $duplicateTypes = []
    ) {
        $this->classMapContext = $classMapContext;
        $this->clientContext = $clientContext;
        $this->typeNamespace = $typeNamespace;
        $this->duplicateTypes = $duplicateTypes;
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

    public function getClientFqcn(): string
    {
        return $this->clientContext->getFqcn();
    }

    public function getClassmapFqcn(): string
    {
        return $this->classMapContext->getFqcn();
    }

    /**
     * @return array|DuplicateType[]
     */
    public function getDuplicateTypes()
    {
        return $this->duplicateTypes;
    }

    /**
     * @return string
     */
    public function getTypeNamespace(): string
    {
        return $this->typeNamespace;
    }
}
