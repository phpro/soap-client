<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

class ConfigContext implements ContextInterface
{
    private $setters = [];
    private $requestRegex = '/Request$/i';
    private $responseRegex = '/Response$/i';

    public function addSetter(string $name, string $value, bool $namespace = false): self
    {
        if ($value === '') {
            return $this;
        }
        if ($namespace) {
            $this->setters[$name] = str_replace('/', '\\\\', $value);

            return $this;
        }
        $this->setters[$name] = addslashes($value);

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
    public function getRequestRegex(): string
    {
        return $this->requestRegex;
    }

    /**
     * @param string $requestRegex
     */
    public function setRequestRegex(string $requestRegex): void
    {
        $this->requestRegex = $requestRegex;
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
     */
    public function setResponseRegex(string $responseRegex): void
    {
        $this->responseRegex = $responseRegex;
    }
}
