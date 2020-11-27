<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Laminas\Code\Generator\Exception\ClassNotFoundException;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Phpro\SoapClient\CodeGenerator\TypeGenerator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class TypeGeneratorSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator
 * @mixin TypeGenerator
 */
class TypeGeneratorSpec extends ObjectBehavior
{

    function let(RuleSetInterface $ruleSet)
    {
        $this->beConstructedWith($ruleSet);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeGenerator::class);
    }

    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_generates_types(RuleSetInterface $ruleSet, FileGenerator $file, ClassGenerator $class)
    {
        $type = new Type(
            $namespace = 'MyNamespace',
            'MyType',
            [new Property('prop1', 'string', $namespace)]
        );
        $property = $type->getProperties()[0];

        $file->generate()->willReturn('code');

        $file->getClass()->willReturn($class);
        $class->setNamespaceName('MyNamespace')->shouldBeCalled();
        $class->setName('MyType')->shouldBeCalled();
        $file->setClass($class)->shouldBeCalled();

        $this->RuleSet_should_apply_rules_for_type($ruleSet, $type);
        $this->RuleSet_should_apply_rules_for_type_and_property($ruleSet, $type, $property);

        $this->generate($file, $type)->shouldReturn('code');
    }

    function it_generates_types_for_file_without_classes(RuleSetInterface $ruleSet, FileGenerator $file, ClassGenerator $class)
    {
        $type = new Type(
            $namespace = 'MyNamespace',
            'MyType',
            [new Property('prop1', 'string', $namespace)]
        );
        $property = $type->getProperties()[0];

        $file->generate()->willReturn('code');

        $file->getClass()->willThrow(new ClassNotFoundException('No class is set'));
        $file->setClass(Argument::that(function (ClassGenerator $class) {
            return $class->getNamespaceName() === 'MyNamespace'
                && $class->getName() === 'MyType';
        }))->shouldBeCalled();

        $this->RuleSet_should_apply_rules_for_type($ruleSet, $type);
        $this->RuleSet_should_apply_rules_for_type_and_property($ruleSet, $type, $property);

        $this->generate($file, $type)->shouldReturn('code');
    }

    private function RuleSet_should_apply_rules_for_type(RuleSetInterface $ruleSet, Type $type)
    {
        $ruleSet->applyRules(Argument::that(function (ContextInterface $context) use ($type) {
            return $context instanceof TypeContext
                && $context->getType() === $type;
        }))->shouldBeCalled();
    }

    private function RuleSet_should_apply_rules_for_type_and_property(RuleSetInterface $ruleSet, Type $type, Property $property)
    {
        $ruleSet->applyRules(Argument::that(function (ContextInterface $context) use ($type, $property) {
            return $context instanceof PropertyContext
                && $context->getType() === $type
                && $context->getProperty() === $property;
        }))->shouldBeCalled();
    }
}
