<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;

/**
 * Class TypeMapRule
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
class TypeMapRule implements RuleInterface
{
    /**
     * @var RuleInterface[]
     */
    private $typeMap;

    /**
     * @var RuleInterface
     */
    private $defaultRule;

    /**
     * TypeMapRule constructor.
     *
     * @param RuleInterface[] $typeMap
     * @param RuleInterface $defaultRule
     */
    public function __construct(array $typeMap, RuleInterface $defaultRule)
    {
        $this->typeMap = $typeMap;
        $this->defaultRule = $defaultRule;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function appliesToContext(ContextInterface $context): bool
    {
        if (!$context instanceof TypeContext && !$context instanceof PropertyContext) {
            return false;
        }

        $type = $context->getType();
        $typeExists = array_key_exists($type->getName(), $this->typeMap);
        // The default rule will be used here:
        if (!$typeExists) {
            return $this->defaultRule->appliesToContext($context);
        }

        // It's possible to define a null rule, which means that no code will be generated.
        /** @var RuleInterface|null $rule */
        $rule = $this->typeMap[$type->getName()];
        if ($rule  === null) {
            return false;
        }

        return $rule->appliesToContext($context);
    }

    /**
     * @param ContextInterface|TypeContext|PropertyContext $context
     */
    public function apply(ContextInterface $context)
    {
        $type = $context->getType();
        $typeName = $type->getName();
        $rule = array_key_exists($typeName, $this->typeMap) ? $this->typeMap[$typeName] : $this->defaultRule;
        $rule->apply($context);
    }
}
