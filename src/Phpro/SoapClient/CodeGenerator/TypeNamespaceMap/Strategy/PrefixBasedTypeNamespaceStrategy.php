<?php

namespace Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy;

use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;

final readonly class PrefixBasedTypeNamespaceStrategy implements TypeNamespaceMapStrategyInterface
{
    public function __invoke(string $xmlns, string $xmlNamespaceName, Destination $fallback): Destination
    {
        $segment = Normalizer::normalizeNamespaceSegment($xmlNamespaceName);
        if ($segment === null) {
            return $fallback;
        }

        return new Destination(
            $fallback->path . '/' . $segment,
            $fallback->namespace . '\\' . $segment
        );
    }
}
