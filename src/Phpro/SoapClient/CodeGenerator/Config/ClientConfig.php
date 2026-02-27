<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

final readonly class ClientConfig
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public string $name,
        public Destination $destination,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function path(): string
    {
        return rtrim($this->destination->path, '/') . '/' . $this->name . '.php';
    }

    /**
     * @return non-empty-string
     */
    public function fqcn(): string
    {
        return $this->destination->namespace . '\\' . $this->name;
    }
}
