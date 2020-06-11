<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\PropertyCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;

final class IntersectDuplicateTypesStrategy implements TypesManipulatorInterface
{
    public function __invoke(TypeCollection $allTypes): TypeCollection
    {
        return new TypeCollection(...array_values($allTypes->reduce(
            function (array $result, Type $type) use ($allTypes) {
                $name = $type->getName();
                if (array_key_exists($name, $result)) {
                    return $result;
                }

                return array_merge(
                    $result,
                    [
                        $name => $this->intersectTypes($allTypes->fetchAllByName($name))
                    ]
                );
            },
            []
        )));
    }

    private function intersectTypes(TypeCollection $duplicateTypes): Type
    {
        return new Type(
            current($duplicateTypes)->getXsdType(),
            iterator_to_array(
                (new PropertyCollection(...array_merge(
                    $duplicateTypes->map(static function (Type $type): array {
                        return $type->getProperties();
                    })
                )))->unique()
            )
        );
    }
}