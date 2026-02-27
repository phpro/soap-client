<?php

namespace Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy;

use Phpro\SoapClient\CodeGenerator\Config\Destination;

/**
 * @psalm-type StrategyCallable = callable(string $xmlns, string $xmlNamespaceName, Destination $fallback): Destination
 */
interface TypeNamespaceMapStrategyInterface
{
    public function __invoke(string $xmlns, string $xmlNamespaceName, Destination $fallback): Destination;
}
