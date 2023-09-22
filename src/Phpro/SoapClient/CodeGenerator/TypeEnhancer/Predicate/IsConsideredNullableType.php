<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer\Predicate;

use Soap\Engine\Metadata\Model\TypeMeta;

class IsConsideredNullableType
{
    public function __invoke(TypeMeta $meta): bool
    {
        if ($meta->isNullable()->unwrapOr(false)) {
            return true;
        }

        if ($meta->isNil()->unwrapOr(false)) {
            return true;
        }

        if ($meta->isAttribute()->unwrapOr(false)) {
            // If no 'use' is defined, XSD attributes get an implicit default "optional" value
            if ($meta->use()->unwrapOr('optional') === 'optional') {
                return true;
            }
        }

        return false;
    }
}
