<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\TypeMapRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TypeMapRuleSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Rules
 * @mixin TypeMapRule
 */
class TypeMapRuleSpec extends ObjectBehavior
{

    function let(RuleInterface $rule, RuleInterface $defaultRule)
    {
        $this->beConstructedWith([
            'SomeType' => $rule,
            'NullType' => null,
        ], $defaultRule);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeMapRule::class);
    }

    function it_is_a_rule()
    {
        $this->shouldImplement(RuleInterface::class);
    }

    function it_can_not_apply_to_regular_context(ContextInterface $context)
    {
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_apply_to_type_context(RuleInterface $rule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'SomeType', []));
        $rule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_to_property_context(RuleInterface $rule, PropertyContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'SomeType', []));
        $rule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_the_default_assembler_to_unknown_types(RuleInterface $defaultRule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'UnknownType', []));
        $defaultRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_not_apply_to_knwon_types_with_no_rule(TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'NullType', []));
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_not_apply_if_rule_does_not_apply(RuleInterface $rule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'SomeType', []));
        $rule->appliesToContext($context)->willReturn(false);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_applies_a_specified_rule_to_known_types(RuleInterface $rule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'SomeType', []));
        $rule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }

    function it_applies_the_default_rule_to_unknown_types(RuleInterface $defaultRule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'UnknownType', []));
        $defaultRule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }
}
