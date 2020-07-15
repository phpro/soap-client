<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Detector;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;

final class DuplicateTypeNamesDetector
{
    /**
     * @param TypeCollection $types
     *
     * @return string[]
     */
    public function __invoke(TypeCollection $types): array
    {
        return array_keys(
            array_filter(
                array_count_values($types->map(
                    static function (Type $type): string {
                        return Normalizer::normalizeClassname($type->getName());
                    }
                )),
                static function (int $count): bool {
                    return $count > 1;
                }
            )
        );
    }
}
