<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\TypenameMatchesRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TypenameMatchesRuleSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Rules
 * @mixin TypenameMatchesRule
 */
class TypenameMatchesRuleSpec extends ObjectBehavior
{

    function let(RuleInterface $subRule)
    {
        $this->beConstructedWith($subRule, '/^TypeName$/');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypenameMatchesRule::class);
    }

    function it_is_a_rule()
    {
        $this->shouldImplement(RuleInterface::class);
    }

    function it_can_not_apply_to_regular_context(ContextInterface $context)
    {
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_apply_to_type_context(RuleInterface $subRule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'TypeName', []));
        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_to_property_context( RuleInterface $subRule, PropertyContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'TypeName', []));
        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_not_apply_on_invalid_regex(RuleInterface $subRule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'InvalidTypeName', []));
        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_apply_if_subrule_does_not_apply(RuleInterface $subRule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'TypeName', []));
        $subRule->appliesToContext($context)->willReturn(false);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_appies_subrule_when_applied(RuleInterface $subRule, ContextInterface $context)
    {
        $subRule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }
}
