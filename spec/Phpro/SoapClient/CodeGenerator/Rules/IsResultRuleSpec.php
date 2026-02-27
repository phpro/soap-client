<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\IsResultRule;
use Phpro\SoapClient\CodeGenerator\Rules\RuleInterface;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\ParameterCollection;
use Soap\Engine\Metadata\Metadata;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class IsResponseRuleSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Rules
 * @mixin IsResponseRule
 */
class IsResultRuleSpec extends ObjectBehavior
{
    function let(Metadata $metadata, RuleInterface $subRule)
    {
        $metadata->getMethods()->willReturn(new MethodCollection(
            new Method(
                'method1',
                new ParameterCollection(
                    new Parameter('prop1', XsdType::create('string'))
                ),
                XsdType::create('Result-Type')
            )
        ));
        $this->beConstructedWith($metadata, $subRule);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IsResultRule::class);
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
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $codeGeneratorContext = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $type = new Type($codeGeneratorContext, 'ResultType', 'ResultType', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type, $codeGeneratorContext);

        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_apply_to_property_context(RuleInterface $subRule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $codeGeneratorContext = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $property = new \Phpro\SoapClient\CodeGenerator\Model\Property('prop1', 'string', $codeGeneratorContext, 'MyNamespace', XsdType::create('string'));
        $type = new Type($codeGeneratorContext, 'ResultType', 'ResultType', [$property], XsdType::create('MyType'));
        $context = new PropertyContext(new \Laminas\Code\Generator\ClassGenerator(), $type, $property, $codeGeneratorContext);

        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(true);
    }

    function it_can_not_apply_on_invalid_type(RuleInterface $subRule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $codeGeneratorContext = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $type = new Type($codeGeneratorContext, 'InvalidTypeName', 'InvalidTypeName', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type, $codeGeneratorContext);

        $subRule->appliesToContext($context)->willReturn(true);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_can_apply_if_subrule_does_not_apply(RuleInterface $subRule)
    {
        $destination = new \Phpro\SoapClient\CodeGenerator\Config\Destination('src/type', 'MyNamespace');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($destination);
        $codeGeneratorContext = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $type = new Type($codeGeneratorContext, 'ResultType', 'ResultType', [], XsdType::create('MyType'));
        $context = new TypeContext(new \Laminas\Code\Generator\ClassGenerator(), $type, $codeGeneratorContext);

        $subRule->appliesToContext($context)->willReturn(false);
        $this->appliesToContext($context)->shouldReturn(false);
    }

    function it_appies_subrule_when_applied(RuleInterface $subRule, ContextInterface $context)
    {
        $subRule->apply($context)->shouldBeCalled();
        $this->apply($context);
    }
}
