<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata\Manipulators;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;

interface TypesManipulatorInterface
{
    /**
     * By implementing this method, you can change a collection of types into a different collection of types.
     * This makes it possible to alter, remove, combine, add, .. types on the fly!
     */
    public function __invoke(TypeCollection $allTypes): TypeCollection;
}
