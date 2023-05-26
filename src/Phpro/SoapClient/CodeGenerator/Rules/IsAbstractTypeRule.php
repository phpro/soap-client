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

class IsAbstractTypeRule implements RuleInterface
{
    private Metadata $metadata;
    private RuleInterface $subRule;

    /**
     * @var list<string>|null
     */
    private $abstractTypes = null;

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
        if (!in_array($type->getName(), $this->listAbstractTypes(), true)) {
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
    private function listAbstractTypes(): array
    {
        if (null === $this->abstractTypes) {
            $this->abstractTypes = $this->metadata->getTypes()->reduce(
                /**
                 * @param list<string> $abstractTypes
                 * @return list<string>
                 */
                static function (array $abstractTypes, Type $type): array {
                    if (!$type->getXsdType()->getMeta()->isAbstract()->unwrapOr(false)) {
                        return $abstractTypes;
                    }

                    return [
                        ...$abstractTypes,
                        Normalizer::normalizeClassname(non_empty_string()->assert($type->getName())),
                    ];
                },
                []
            );
        }

        return $this->abstractTypes;
    }
}
