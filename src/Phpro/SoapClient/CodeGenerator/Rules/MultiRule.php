<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;

/**
 * Class MultiRule
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
class MultiRule implements RuleInterface
{
    /**
     * @var array|RuleInterface[]
     */
    private $rules;

    /**
     * MultiRule constructor.
     *
     * @param RuleInterface[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function appliesToContext(ContextInterface $context): bool
    {
        return true;
    }

    /**
     * @param ContextInterface $context
     */
    public function apply(ContextInterface $context)
    {
        foreach ($this->rules as $rule) {
            $this->applyRule($rule, $context);
        }
    }

    /**
     * @param RuleInterface    $rule
     * @param ContextInterface $context
     */
    private function applyRule(RuleInterface $rule, ContextInterface $context)
    {
        if (!$rule->appliesToContext($context)) {
            return;
        }

        $rule->apply($context);
    }
}
