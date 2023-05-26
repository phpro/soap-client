<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\IsExtendingTypeRule;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Metadata;
use Soap\Engine\Metadata\Model\Type as MetaType;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class IsExtendingTypeRuleSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Rules
 * @mixin IsExtendingTypeRule
 */
class IsExtendingTypeRuleSpec extends ObjectBehavior
{
    function let(Metadata $metadata, RuleInterface $subRule)
    {
        $metadata->getTypes()->willReturn(new TypeCollection(
            new MetaType(
                XsdType::create('MyExtending')->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withExtends([
                        'type' => 'BaseType',
                        'namespace' => 'xxx',
                    ])
                ),
                new PropertyCollection()
            ),
            new MetaType(
                XsdType::create('NotExtending'),
                new PropertyCollection()
            ),
        ));
        $this->beConstructedWith($metadata, $subRule);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IsExtendingTypeRule::class);
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
        $context->getType()->willReturn(new Type('MyNamespace', 'MyExtending', [], new TypeMeta()));
        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_to_property_context(RuleInterface $subRule, PropertyContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'MyExtending', [], new TypeMeta()));
        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_not_apply_on_invalid_type(RuleInterface $subRule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'NotExtending', [], new TypeMeta()));
        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_apply_if_subrule_does_not_apply(RuleInterface $subRule, TypeContext $context)
    {
        $context->getType()->willReturn(new Type('MyNamespace', 'MyExtending', [], new TypeMeta()));
        $subRule->appliesToContext($context)->willReturn(false);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_appies_subrule_when_applied(RuleInterface $subRule, ContextInterface $context)
    {
        $subRule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }
}
