<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;

/**
 * Class ClientMethodMatchesRule
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
class ClientMethodMatchesRule implements RuleInterface
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
        if (!$context instanceof ClientMethodContext) {
            return false;
        }

        $method = $context->getMethod();
        if (!preg_match($this->regex, $method->getMethodName())) {
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
