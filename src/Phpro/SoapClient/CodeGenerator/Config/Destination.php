<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

final readonly class Destination
{
    /**
     * @param non-empty-string $path
     * @param non-empty-string $namespace
     */
    public function __construct(
        public string $path,
        public string $namespace,
    ) {
    }
}
