<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\ArrayBoundsCalculator;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\EnumValuesCalculator;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\UnionTypesCalculator;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Predicate\IsConsideredNullableType;
use Soap\Engine\Metadata\Model\TypeMeta;

final class MetaTypeEnhancer implements TypeEnhancer
{
    public function __construct(
        private TypeMeta $meta
    ) {
    }

    /**
     * @param non-empty-string $type
     * @return non-empty-string
     */
    public function asDocBlockType(string $type): string
    {
        $type = match (true) {
            (bool) $this->meta->enums()->unwrapOr([]) => (new EnumValuesCalculator())($this->meta),
            (bool) $this->meta->unions()->unwrapOr([]) => (new UnionTypesCalculator())($this->meta),
            default => $type
        };

        $isArray = $this->meta->isList()->unwrapOr(false);
        $isAttribute = $this->meta->isAttribute()->unwrapOr(false);
        if ($isArray) {
            // Attribute types can be simple types.
            // From the meta, we currently don't know what the base type of this simple type is.
            // TODO : what about string | integer | ... types - from them we know the type?
            $valueType = $isAttribute ? 'mixed' : $type;
            $type = 'array<'.(new ArrayBoundsCalculator())($this->meta).', '.$valueType.'>';
        }

        $isNullable = (new IsConsideredNullableType())($this->meta);
        if ($isNullable) {
            $type = 'null | '.$type;
        }

        return $type;
    }

    /**
     * @param non-empty-string $type
     * @return non-empty-string
     */
    public function asPhpType(string $type): string
    {
        $unions = (bool) $this->meta->unions()->unwrapOr([]);
        if ($unions) {
            $type = 'mixed';
        }

        $isArray = $this->meta->isList()->unwrapOr(false);
        if ($isArray) {
            $type = 'array';
        }

        $isNullable = (new IsConsideredNullableType())($this->meta);
        if ($isNullable && $type !== 'mixed') {
            $type = '?'.$type;
        }

        return $type;
    }
}
