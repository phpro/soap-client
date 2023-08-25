<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer\Predicate;

use function Psl\Iter\contains;

final class IsLocatedInBaseNamespace
{
    /** @var list<string> */
    private static array $baseSchemas = [
        'http://www.w3.org/2001/XMLSchema',
        'http://www.w3.org/XML/1998/namespace',
    ];

    public function __invoke(string $namespace): bool
    {
        return contains(self::$baseSchemas, $namespace);
    }
}
