<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\ExtSoap\Metadata\Detector;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Type\non_empty_string;

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
                        return Normalizer::normalizeClassname(non_empty_string()->assert($type->getName()));
                    }
                )),
                static function (int $count): bool {
                    return $count > 1;
                }
            )
        );
    }
}
