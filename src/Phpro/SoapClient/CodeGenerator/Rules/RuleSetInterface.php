<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;

interface RuleSetInterface
{
    public function applyRules(ContextInterface $context);
}
