<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;

interface RuleInterface
{
    public function appliesToContext(ContextInterface $context): bool;

    public function apply(ContextInterface $context);
}
