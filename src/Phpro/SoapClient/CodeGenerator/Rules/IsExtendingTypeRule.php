<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Metadata;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Type\non_empty_string;

class IsExtendingTypeRule implements RuleInterface
{
    private Metadata $metadata;
    private RuleInterface $subRule;

    /**
     * @var list<string>|null
     */
    private $extendingTypes = null;

    public function __construct(Metadata $metadata, RuleInterface $subRule)
    {
        $this->metadata = $metadata;
        $this->subRule = $subRule;
    }

    public function appliesToContext(ContextInterface $context): bool
    {
        if (!$context instanceof TypeContext && !$context instanceof PropertyContext) {
            return false;
        }

        $type = $context->getType();
        if (!in_array($type->getName(), $this->listExtendingTypes(), true)) {
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
    private function listExtendingTypes(): array
    {
        if (null === $this->extendingTypes) {
            $this->extendingTypes = $this->metadata->getTypes()->reduce(
                /**
                 * @param list<string> $extendingTypes
                 * @return list<string>
                 */
                static function (array $extendingTypes, Type $type): array {
                    if (!$type->getXsdType()->getMeta()->extends()->unwrapOr(false)) {
                        return $extendingTypes;
                    }

                    return [
                        ...$extendingTypes,
                        Normalizer::normalizeClassname(non_empty_string()->assert($type->getName()))
                    ];
                },
                []
            );
        }

        return $this->extendingTypes;
    }
}
