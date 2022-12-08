<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorInterface;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Type\non_empty_string;

final class IntersectDuplicateTypesStrategy implements TypesManipulatorInterface
{
    public function __invoke(TypeCollection $allTypes): TypeCollection
    {
        return new TypeCollection(...array_values($allTypes->reduce(
            function (array $result, Type $type) use ($allTypes): array {
                $name = Normalizer::normalizeClassname(non_empty_string()->assert($type->getName()));
                if (array_key_exists($name, $result)) {
                    return $result;
                }

                return array_merge(
                    $result,
                    [
                        $name => $this->intersectTypes($this->fetchAllTypesNormalizedByName($allTypes, $name))
                    ]
                );
            },
            []
        )));
    }

    private function intersectTypes(TypeCollection $duplicateTypes): Type
    {
        return new Type(
            current(iterator_to_array($duplicateTypes))->getXsdType(),
            $this->uniqueProperties(
                new PropertyCollection(...array_merge(
                    ...$duplicateTypes->map(
                        static fn (Type $type): array => iterator_to_array($type->getProperties())
                    )
                ))
            )
        );
    }

    private function fetchAllTypesNormalizedByName(TypeCollection $types, string $name): TypeCollection
    {
        return $types->filter(static function (Type $type) use ($name): bool {
            return Normalizer::normalizeClassname(non_empty_string()->assert($type->getName())) === $name;
        });
    }

    private function uniqueProperties(PropertyCollection $props): PropertyCollection
    {
        return new PropertyCollection(...array_values(
            array_combine(
                $props->map(static fn (Property $prop) => $prop->getName()),
                iterator_to_array($props)
            )
        ));
    }
}
