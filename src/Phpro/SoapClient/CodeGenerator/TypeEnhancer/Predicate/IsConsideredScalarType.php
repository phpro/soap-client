<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer\Predicate;

use Soap\Engine\Metadata\Model\TypeMeta;

class IsConsideredScalarType
{
    public function __invoke(TypeMeta $meta): bool
    {
        return $meta->isSimple()->unwrapOr(false)
            || $meta->isAttribute()->unwrapOr(false);
    }
}
