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

class IsAbstractTypeRule implements RuleInterface
{
    /**
     * @var list<string>|null
     */
    private $abstractTypes = null;

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
        if (!in_array($type->getName(), $this->listAbstractTypes($codingStandards), true)) {
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
    private function listAbstractTypes(CodingStandardsStrategyInterface $codingStandards): array
    {
        if (null === $this->abstractTypes) {
            $this->abstractTypes = $this->metadata->getTypes()->reduce(
                /**
                 * @param list<string> $abstractTypes
                 * @return list<string>
                 */
                static function (array $abstractTypes, Type $type) use ($codingStandards): array {
                    if (!$type->getXsdType()->getMeta()->isAbstract()->unwrapOr(false)) {
                        return $abstractTypes;
                    }

                    return [
                        ...$abstractTypes,
                        $codingStandards->normalizeTypeName(non_empty_string()->assert($type->getName())),
                    ];
                },
                []
            );
        }

        return $this->abstractTypes;
    }
}
