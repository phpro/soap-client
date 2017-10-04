<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;

/**
 * Interface RuleInterface
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
interface RuleInterface
{
    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function appliesToContext(ContextInterface $context): bool;

    /**
     * @param ContextInterface $context
     */
    public function apply(ContextInterface $context);
}
