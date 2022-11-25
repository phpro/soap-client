<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\ExtSoap\Metadata\Detector\DuplicateTypeNamesDetector;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Type\non_empty_string;

final class RemoveDuplicateTypesStrategy implements TypesManipulatorInterface
{
    public function __invoke(TypeCollection $types): TypeCollection
    {
        $duplicateNames = (new DuplicateTypeNamesDetector())($types);

        return $types->filter(static function (Type $type) use ($duplicateNames): bool {
            return !in_array(
                Normalizer::normalizeClassname(non_empty_string()->assert($type->getName())),
                $duplicateNames,
                true
            );
        });
    }
}
