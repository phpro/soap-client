<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Type\non_empty_string;

final class DuplicateTypesKey
{
    /**
     * Generates a unique key for grouping/detecting duplicate types.
     * When context is provided, types in different target PHP namespaces
     * are NOT considered duplicates (they produce different keys).
     */
    public static function forType(Type $type, CodeGeneratorContext $context): string
    {
        $codingStandards = $context->codingStandards;
        $normalizedName = $codingStandards->normalizeTypeName(non_empty_string()->assert($type->getName()));
        $destination = $context->typeNamespaceMap->detectDestinationForType($type->getXsdType());

        return $destination->namespace . '\\' . $normalizedName;
    }
}
