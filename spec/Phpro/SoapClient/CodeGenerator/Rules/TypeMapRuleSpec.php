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
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

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

    function it_can_apply_to_type_context(RuleInterface $rule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'SomeType', 'SomeType', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type);

        $rule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_to_property_context(RuleInterface $rule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $property = new \Phpro\SoapClient\CodeGenerator\Model\Property('prop1', 'string', $namespaceMap, 'MyNamespace', XsdType::create('string'));
        $type = new Type($namespaceMap, 'SomeType', 'SomeType', [$property], XsdType::create('MyType'));
        $context = new PropertyContext(new \Laminas\Code\Generator\ClassGenerator(), $type, $property);

        $rule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_the_default_assembler_to_unknown_types(RuleInterface $defaultRule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'UnknownType', 'UnknownType', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type);

        $defaultRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_not_apply_to_knwon_types_with_no_rule()
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'NullType', 'NullType', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type);

        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_not_apply_if_rule_does_not_apply(RuleInterface $rule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'SomeType', 'SomeType', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type);

        $rule->appliesToContext($context)->willReturn(false);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_applies_a_specified_rule_to_known_types(RuleInterface $rule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'SomeType', 'SomeType', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type);

        $rule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }

    function it_applies_the_default_rule_to_unknown_types(RuleInterface $defaultRule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'UnknownType', 'UnknownType', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type);

        $defaultRule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }
}
