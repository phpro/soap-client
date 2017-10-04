<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;

/**
 * Class TypenameMatchingAssembleRule
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
class TypenameMatchesRule implements RuleInterface
{
    /**
     * @var RuleInterface
     */
    private $subRule;

    /**
     * @var string
     */
    private $regex;

    /**
     * TypenameMatchingAssembleRule constructor.
     *
     * @param RuleInterface $subRule
     * @param string        $regex
     */
    public function __construct(RuleInterface $subRule, string $regex)
    {
        $this->subRule = $subRule;
        $this->regex = $regex;
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
        if (!preg_match($this->regex, $type->getName())) {
            return false;
        }


        return $this->subRule->appliesToContext($context);
    }

    /**
     * @param ContextInterface $context
     */
    public function apply(ContextInterface $context)
    {
        $this->subRule->apply($context);
    }
}
