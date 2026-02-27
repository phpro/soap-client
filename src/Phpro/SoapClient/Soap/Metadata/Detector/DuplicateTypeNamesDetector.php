<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Metadata\Detector;

use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;
use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Type\non_empty_string;

final class DuplicateTypeNamesDetector
{
    public function __construct(
        private CodingStandardsStrategyInterface $codingStandards = new DefaultCodingStandardsStrategy(),
    ) {
    }

    /**
     * @param TypeCollection $types
     *
     * @return string[]
     */
    public function __invoke(TypeCollection $types): array
    {
        $codingStandards = $this->codingStandards;

        return array_keys(
            array_filter(
                array_count_values($types->map(
                    static function (Type $type) use ($codingStandards): string {
                        return $codingStandards->normalizeTypeName(non_empty_string()->assert($type->getName()));
                    }
                )),
                static function (int $count): bool {
                    return $count > 1;
                }
            )
        );
    }
}
