<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

class ConfigContext implements ContextInterface
{
    private $setters = [];

    /**
     * @var string
     */
    private $wsdl;

    private bool $generateDocblocks = true;

    public function addSetter(string $name, string $value): self
    {
        $this->setters[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getSetters(): array
    {
        return $this->setters;
    }

    /**
     * @return string
     */
    public function getWsdl(): string
    {
        return $this->wsdl;
    }

    /**
     * @param string $wsdl
     * @return ConfigContext
     */
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
