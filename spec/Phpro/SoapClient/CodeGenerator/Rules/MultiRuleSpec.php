<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\MultiRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class MultiRuleSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Rules
 * @mixin MultiRule
 */
class MultiRuleSpec extends ObjectBehavior
{

    function let(RuleInterface $rule1, RuleInterface $rule2)
    {
        $this->beConstructedWith([$rule1, $rule2]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MultiRule::class);
    }

    function it_is_a_rule()
    {
        $this->shouldImplement(RuleInterface::class);
    }

    function it_can_always_apply_to_any_context(ContextInterface $context)
    {
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_to_multiple_other_rules(RuleInterface $rule1, RuleInterface $rule2, ContextInterface $context)
    {
        $rule1->appliesToContext($context)->willReturn(false);
        $rule1->apply($context)->shouldNotBeCalled();

        $rule2->appliesToContext($context)->willReturn(true);
        $rule2->apply($context)->shouldBeCalled();

        $this->apply($context);
    }
}
