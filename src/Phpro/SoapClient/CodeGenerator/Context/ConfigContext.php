<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

class ConfigContext implements ContextInterface
{
    private $setters = [];

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var string
     */
    private $requestRegex = '';

    /**
     * @var string
     */
    private $responseRegex = '';

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

    /**
     * @return string
     */
    public function getRequestRegex(): string
    {
        return $this->requestRegex;
    }

    /**
     * @param string $requestRegex
     * @return ConfigContext
     */
    public function setRequestRegex(string $requestRegex): self
    {
        $this->requestRegex = $requestRegex;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseRegex(): string
    {
        return $this->responseRegex;
    }

    /**
     * @param string $responseRegex
     * @return ConfigContext
     */
    public function setResponseRegex(string $responseRegex): self
    {
        $this->responseRegex = $responseRegex;

        return $this;
    }
}
