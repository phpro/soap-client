<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;

/**
 * Class PropertynameMatchesRule
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
class PropertynameMatchesRule implements RuleInterface
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
        if (!$context instanceof PropertyContext) {
            return false;
        }

        $property = $context->getProperty();
        if (!preg_match($this->regex, $property->getName())) {
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
