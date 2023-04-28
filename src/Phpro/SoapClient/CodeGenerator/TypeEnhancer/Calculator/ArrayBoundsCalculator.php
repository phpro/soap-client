<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator;

use Soap\Engine\Metadata\Model\TypeMeta;

final class ArrayBoundsCalculator
{
    public function __invoke(TypeMeta $meta): string
    {
        $min = $meta->minOccurs()
            ->map(fn (int $min): string => $min === -1 ? 'min' : (string) $min)
            ->unwrapOr('min');

        $max = $meta->maxOccurs()
            ->map(fn (int $max): string => $max === -1 ? 'max' : (string) $max)
            ->unwrapOr('max');

        return 'int<'.$min.','.$max.'>';
    }
}
