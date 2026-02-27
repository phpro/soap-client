<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;

final readonly class Client
{
    public function __construct(
        private ClientConfig $clientConfig,
        private ClientMethodMap $methods
    ) {
    }

    public function config(): ClientConfig
    {
        return $this->clientConfig;
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
    public function getName(): string
    {
        return $this->clientConfig->name;
    }

    public function getMethodMap(): ClientMethodMap
    {
        return $this->methods;
    }
}
