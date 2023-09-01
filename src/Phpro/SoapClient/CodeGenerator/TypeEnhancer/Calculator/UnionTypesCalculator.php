<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\TypeMeta;
use function Psl\Dict\unique;
use function Psl\Str\join;
use function Psl\Type\non_empty_string;
use function Psl\Vec\map;

final class UnionTypesCalculator
{
    /**
     * @param TypeMeta $meta
     * @return non-empty-string
     */
    public function __invoke(TypeMeta $meta): string
    {
        return non_empty_string()->assert(
            join(
                unique(
                    map(
                        $meta->unions()->unwrapOr([]),
                        /**
                         * @var array{type: non-empty-string, isList: bool} $union
                         * @return non-empty-string
                         */
                        static function (array $union): string {
                            $type = $union['type'];
                            $type = Normalizer::isKnownType($type) ? $type : 'mixed'; // Todo : resolve bottom type of the selected simple type??

                            return $union['isList'] ? 'list<'.$type.'>' : $type;
                        }
                    )
                ),
                ' | '
            )
        );
    }
}
