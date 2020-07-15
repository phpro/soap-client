<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Detector\DuplicateTypeNamesDetector;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;

final class RemoveDuplicateTypesStrategy implements TypesManipulatorInterface
{
    public function __invoke(TypeCollection $types): TypeCollection
    {
        $duplicateNames = (new DuplicateTypeNamesDetector())($types);

        return $types->filter(static function (Type $type) use ($duplicateNames): bool {
            return !in_array(Normalizer::normalizeClassname($type->getName()), $duplicateNames, true);
        });
    }
}
