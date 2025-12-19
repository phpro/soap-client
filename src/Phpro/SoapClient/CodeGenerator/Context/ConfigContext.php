<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

final class ConfigContext implements ContextInterface
{
    /**
     * @var array<string, string>
     */
    private array $setters = [];

    private string $wsdl = '';

    private bool $generateDocblocks = true;

    public function addSetter(string $name, string $value): self
    {
        $this->setters[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getSetters(): array
    {
        return $this->setters;
    }

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
}
