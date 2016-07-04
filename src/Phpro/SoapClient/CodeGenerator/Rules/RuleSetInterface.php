<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;

/**
 * Interface RuleSetInterface
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
interface RuleSetInterface
{
    /**
     * @param ContextInterface $context
     */
    public function applyRules(ContextInterface $context);
}
