<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Metadata\Manipulators\DuplicateTypes;

use Closure;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\Soap\Metadata\Manipulators\TypesManipulatorInterface;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type;

final class RemoveDuplicateTypesStrategy implements TypesManipulatorInterface
{
    public function __construct(
        private CodeGeneratorContext $context
    ) {
    }

    /**
     * Factory that returns a closure - Config will call it with the context.
     *
     * @return Closure(CodeGeneratorContext): self
     */
    public static function create(): Closure
    {
        return static fn (CodeGeneratorContext $context) => new self($context);
    }

    public function __invoke(TypeCollection $types): TypeCollection
    {
        $duplicateKeys = $this->detectDuplicateKeys($types);

        return $types->filter(
            fn (Type $type): bool => !in_array(
                DuplicateTypesKey::forType($type, $this->context),
                $duplicateKeys,
                true
            )
        );
    }

    /**
     * @return list<string>
     */
    private function detectDuplicateKeys(TypeCollection $types): array
    {
        $keyCounts = [];
        foreach ($types as $type) {
            $key = DuplicateTypesKey::forType($type, $this->context);
            $keyCounts[$key] = ($keyCounts[$key] ?? 0) + 1;
        }

        return array_keys(array_filter($keyCounts, static fn (int $count): bool => $count > 1));
    }
}
