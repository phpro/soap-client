<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Rules;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use Phpro\SoapClient\CodeGenerator\Rules\TypenameMatchesRule;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Metadata\Model\XsdType;

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

    function it_can_apply_to_type_context(RuleInterface $subRule)
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'TypeName', 'TypeName', [], XsdType::create('MyType'));
        $context = new TypeContext(new ClassGenerator(), $type);

        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_to_property_context(RuleInterface $subRule)
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $property = new Property('prop1', 'string', $namespaceMap, 'MyNamespace', XsdType::create('string'));
        $type = new Type($namespaceMap, 'TypeName', 'TypeName', [$property], XsdType::create('MyType'));
        $context = new PropertyContext(new ClassGenerator(), $type, $property);

        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_not_apply_on_invalid_regex(RuleInterface $subRule)
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'InvalidTypeName', 'InvalidTypeName', [], XsdType::create('MyType'));
        $context = new TypeContext(new ClassGenerator(), $type);

        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_apply_if_subrule_does_not_apply(RuleInterface $subRule)
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $type = new Type($namespaceMap, 'TypeName', 'TypeName', [], XsdType::create('MyType'));
        $context = new TypeContext(new ClassGenerator(), $type);

        $subRule->appliesToContext($context)->willReturn(false);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_appies_subrule_when_applied(RuleInterface $subRule, ContextInterface $context)
    {
        $subRule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }
}
