<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator;

use Soap\Engine\Metadata\Model\TypeMeta;
use function Psl\Str\join;
use function Psl\Type\non_empty_string;
use function Psl\Vec\map;

final class EnumValuesCalculator
{
    /**
     * @param TypeMeta $meta
     * @return non-empty-string
     */
    public function __invoke(TypeMeta $meta): string
    {
        return non_empty_string()->assert(
            join(
                map(
                    $meta->enums()->unwrapOr([]),
                    static fn (string $value) => "'".addcslashes($value, "'")."'",
                ),
                ' | '
            )
        );
    }
}
