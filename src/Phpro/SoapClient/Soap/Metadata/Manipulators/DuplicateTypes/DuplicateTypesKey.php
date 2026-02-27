<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Type\non_empty_string;

final class DuplicateTypesKey
{
    /**
     * Generates a unique key for grouping/detecting duplicate types.
     * When namespace map is provided, types in different target PHP namespaces
     * are NOT considered duplicates (they produce different keys).
     */
    public static function forType(Type $type, ?TypeNamespaceMap $namespaceMap): string
    {
        $normalizedName = Normalizer::normalizeClassname(non_empty_string()->assert($type->getName()));

        if ($namespaceMap === null) {
            return $normalizedName;
        }

        $destination = $namespaceMap->detectDestinationForType($type->getXsdType());

        return $destination->namespace . '\\' . $normalizedName;
    }
}
