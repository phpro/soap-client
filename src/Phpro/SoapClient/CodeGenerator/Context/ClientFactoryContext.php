<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Config\Config;

class ClientFactoryContext implements ContextInterface
{
    private $clientName;
    private $clientNamespace;
    private $classmapName;
    private $classmapNamespace;

    public function __construct(
        string $clientName,
        string $clientNamespace,
        string $classmapName,
        string $classmapNamespace
    ) {
        $this->clientName = $clientName;
        $this->clientNamespace = $clientNamespace;
        $this->classmapName = $classmapName;
        $this->classmapNamespace = $classmapNamespace;
    }

    public static function fromConfig(Config $config): self
    {
        return new self(
            $config->getClientName(),
            $config->getClientNamespace(),
            $config->getClassMapName(),
            $config->getClassMapNamespace()
        );
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function getClientNamespace(): string
    {
        return $this->clientNamespace;
    }

    public function getClassmapName(): string
    {
        return $this->classmapName;
    }

    public function getClassmapNamespace(): string
    {
        return $this->classmapNamespace;
    }

    public function getClientFqcn(): string
    {
        return $this->clientNamespace.'\\'.$this->clientName;
    }

    public function getClassmapFqcn(): string
    {
        return $this->classmapNamespace.'\\'.$this->classmapName;
    }
}
