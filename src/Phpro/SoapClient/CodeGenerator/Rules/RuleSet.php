<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;

/**
 * Class RuleSet
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
class RuleSet implements RuleSetInterface
{

    /**
     * @var array|RuleInterface[]
     */
    private $rules = [];

    /**
     * RuleSet constructor.
     *
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @param ContextInterface $context
     */
    public function applyRules(ContextInterface $context)
    {
        foreach ($this->rules as $rule) {
            if (!$rule->appliesToContext($context)) {
                continue;
            }

            $rule->apply($context);
        }
    }
}
