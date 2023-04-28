<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\ArrayBoundsCalculator;
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
        $isArray = $this->meta->isList()->unwrapOr(false);
        if ($isArray) {
            $type = 'array<'.(new ArrayBoundsCalculator())($this->meta).', '.$type.'>';
        }

        $isNullable = $this->meta->isNullable()->unwrapOr(false);
        if ($isNullable) {
            $type = 'null|'.$type;
        }

        return $type;
    }

    /**
     * @param non-empty-string $type
     * @return non-empty-string
     */
    public function asPhpType(string $type): string
    {
        $isArray = $this->meta->isList()->unwrapOr(false);
        if ($isArray) {
            $type = 'array';
        }

        $isNullable = $this->meta->isNullable()->unwrapOr(false);
        if ($isNullable) {
            $type = '?'.$type;
        }

        return $type;
    }
}
