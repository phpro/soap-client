<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Soap\Engine\Metadata\Metadata;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Type\non_empty_string;

class IsExtendingTypeRule implements RuleInterface
{
    /**
     * @var list<string>|null
     */
    private $extendingTypes = null;

    public function __construct(
        private Metadata $metadata,
        private RuleInterface $subRule,
    ) {
    }

    public function appliesToContext(ContextInterface $context): bool
    {
        if (!$context instanceof TypeContext && !$context instanceof PropertyContext) {
            return false;
        }

        $type = $context->getType();
        $codingStandards = $context->getCodeGeneratorContext()->codingStandards;
        if (!in_array($type->getName(), $this->listExtendingTypes($codingStandards), true)) {
            return false;
        }

        return $this->subRule->appliesToContext($context);
    }

    public function apply(ContextInterface $context)
    {
        $this->subRule->apply($context);
    }

    /**
     * @return list<string>
     */
    private function listExtendingTypes(CodingStandardsStrategyInterface $codingStandards): array
    {
        if (null === $this->extendingTypes) {
            $this->extendingTypes = $this->metadata->getTypes()->reduce(
                /**
                 * @param list<string> $extendingTypes
                 * @return list<string>
                 */
                static function (array $extendingTypes, Type $type) use ($codingStandards): array {
                    if (!$type->getXsdType()->getMeta()->extends()->unwrapOr(false)) {
                        return $extendingTypes;
                    }

                    return [
                        ...$extendingTypes,
                        $codingStandards->normalizeTypeName(non_empty_string()->assert($type->getName()))
                    ];
                },
                []
            );
        }

        return $this->extendingTypes;
    }
}
