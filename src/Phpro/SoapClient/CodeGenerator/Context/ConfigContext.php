<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;

final class ConfigContext implements ContextInterface
{
    private string $wsdl = '';
    private bool $generateDocblocks = true;
    private ?Destination $typeDestination = null;
    private ?ClientConfig $clientConfig = null;
    private ?ClassMapConfig $classMapConfig = null;
    /** @var array<string, string> */
    private array $detectedXmlNamespaces = [];

    public function getWsdl(): string
    {
        return $this->wsdl;
    }

    public function setWsdl(string $wsdl): self
    {
        $this->wsdl = $wsdl;

        return $this;
    }

    public function setGenerateDocblocks(bool $generateDocblocks): self
    {
        $this->generateDocblocks = $generateDocblocks;

        return $this;
    }

    public function isGenerateDocblocks(): bool
    {
        return $this->generateDocblocks;
    }

    public function getTypeDestination(): ?Destination
    {
        return $this->typeDestination;
    }

    public function setTypeDestination(Destination $typeDestination): self
    {
        $this->typeDestination = $typeDestination;

        return $this;
    }

    public function getClientConfig(): ?ClientConfig
    {
        return $this->clientConfig;
    }

    public function setClientConfig(ClientConfig $clientConfig): self
    {
        $this->clientConfig = $clientConfig;

        return $this;
    }

    public function getClassMapConfig(): ?ClassMapConfig
    {
        return $this->classMapConfig;
    }

    public function setClassMapConfig(ClassMapConfig $classMapConfig): self
    {
        $this->classMapConfig = $classMapConfig;

        return $this;
    }

    /** @return array<string, string> */
    public function getDetectedXmlNamespaces(): array
    {
        return $this->detectedXmlNamespaces;
    }

    /** @param array<string, string> $xmlNamespaces */
    public function setDetectedXmlNamespaces(array $xmlNamespaces): self
    {
        $this->detectedXmlNamespaces = $xmlNamespaces;

        return $this;
    }
}
