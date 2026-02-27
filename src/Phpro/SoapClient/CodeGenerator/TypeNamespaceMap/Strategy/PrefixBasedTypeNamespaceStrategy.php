<?php

namespace Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy;

use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;
use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\Destination;

final readonly class PrefixBasedTypeNamespaceStrategy implements TypeNamespaceMapStrategyInterface
{
    public function __construct(
        private CodingStandardsStrategyInterface $codingStandards = new DefaultCodingStandardsStrategy(),
    ) {
    }

    public function __invoke(string $xmlns, string $xmlNamespaceName, Destination $fallback): Destination
    {
        $segment = $this->codingStandards->normalizeNamespaceSegment($xmlNamespaceName);
        if ($segment === null) {
            return $fallback;
        }

        return new Destination(
            $fallback->path . '/' . $segment,
            $fallback->namespace . '\\' . $segment
        );
    }
}
