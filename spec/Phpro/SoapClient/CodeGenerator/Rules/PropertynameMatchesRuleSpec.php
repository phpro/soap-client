<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\PropertynameMatchesRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PropertynameMatchesRuleSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Rules
 * @mixin PropertynameMatchesRule
 */
class PropertynameMatchesRuleSpec extends ObjectBehavior
{

    function let(RuleInterface $subRule)
    {
        $this->beConstructedWith($subRule, '/^myProperty/');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PropertynameMatchesRule::class);
    }

    function it_is_a_rule()
    {
        $this->shouldImplement(RuleInterface::class);
    }

    function it_can_not_apply_to_regular_context(ContextInterface $context)
    {
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_apply_to_property_context( RuleInterface $subRule, PropertyContext $context)
    {
        $context->getProperty()->willReturn(new Property('myProperty', 'string', 'ns1'));
        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_not_apply_on_invalid_regex(RuleInterface $subRule, PropertyContext $context)
    {
        $context->getProperty()->willReturn(new Property('InvalidTypeName', 'string', 'ns1'));
        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_apply_if_subrule_does_not_apply(RuleInterface $subRule, PropertyContext $context)
    {
        $context->getProperty()->willReturn(new Property('MyProperty', 'string', 'ns1'));
        $subRule->appliesToContext($context)->willReturn(false);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_appies_subrule_when_applied(RuleInterface $subRule, ContextInterface $context)
    {
        $subRule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }
}
