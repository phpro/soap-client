<?php

namespace Phpro\SoapClient\CodeGenerator\CodingStandards;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;

final readonly class DefaultCodingStandardsStrategy implements CodingStandardsStrategyInterface
{
    public function normalizeTypeName(string $name): string
    {
        return Normalizer::normalizeClassname($name);
    }

    public function normalizeOperationName(string $method): string
    {
        return Normalizer::normalizeMethodName($method);
    }

    public function normalizeNamespaceSegment(string $segment): ?string
    {
        return Normalizer::normalizeNamespaceSegment($segment);
    }

    public function normalizeEnumCaseName(string $value): string
    {
        return Normalizer::normalizeEnumCaseName($value);
    }

    public function generatePropertyAccessorMethodName(string $prefix, string $property): string
    {
        return Normalizer::generatePropertyMethod($prefix, $property);
    }

    public function normalizeParameterName(string $propertyName): string
    {
        return $propertyName;
    }
}
