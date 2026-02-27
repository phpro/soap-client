<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Laminas\Code\Generator\Exception\ClassNotFoundException;
use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\ClientGenerator;
use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\FileContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\CodeGenerator\Model\ReturnType;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\FileGenerator;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class ClientGeneratorSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator
 * @mixin ClassMapGenerator
 */
class ClientGeneratorSpec extends ObjectBehavior
{
    function let(RuleSetInterface $ruleSet)
    {
        $this->beConstructedWith($ruleSet);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientGenerator::class);
    }

    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_generates_clients(RuleSetInterface $ruleSet, FileGenerator $file, ClassGenerator $class)
    {
        $typeNamespaceMap = TypeNamespaceMap::create(new Destination('/app', ''));
        $codeGeneratorContext = new CodeGeneratorContext($typeNamespaceMap, new DefaultCodingStandardsStrategy());
        $method = new ClientMethod(
            'Test',
            [new Parameter('parameters', 'Test', $codeGeneratorContext, XsdType::create('Test'))],
            ReturnType::fromMetaData($codeGeneratorContext, XsdType::create('TestResponse')),
            $codeGeneratorContext,
            new MethodMeta()
        );
        $ruleSet->applyRules(Argument::type(ClientMethodContext::class))->shouldBeCalled();
        $ruleSet->applyRules(Argument::type(ClientContext::class))->shouldBeCalled();
        $ruleSet->applyRules(Argument::type(FileContext::class))->shouldBeCalled();
        $file->generate()->willReturn('code');

        $file->getClass()->willThrow(new ClassNotFoundException('No class is set'));
        $file->setClass(Argument::type(ClassGenerator::class))->shouldBeCalled();

        $client = new Client(
            new ClientConfig('MyClient', new Destination('', 'MyNamespace')),
            new ClientMethodMap([$method])
        );
        $this->generate($file, $client)->shouldReturn('code');
    }

    function it_generates_clients_for_file_without_classes(RuleSetInterface $ruleSet, FileGenerator $file, ClassGenerator $class)
    {
        $typeNamespaceMap = TypeNamespaceMap::create(new Destination('/app', ''));
        $codeGeneratorContext = new CodeGeneratorContext($typeNamespaceMap, new DefaultCodingStandardsStrategy());
        $method = new ClientMethod(
            'Test',
            [new Parameter('parameters', 'Test', $codeGeneratorContext, XsdType::create('Test'))],
            ReturnType::fromMetaData($codeGeneratorContext, XsdType::create('TestResponse')),
            $codeGeneratorContext,
            new MethodMeta()
        );

        $ruleSet->applyRules(Argument::type(ClientMethodContext::class))->shouldBeCalled();
        $ruleSet->applyRules(Argument::type(ClientContext::class))->shouldBeCalled();
        $ruleSet->applyRules(Argument::type(FileContext::class))->shouldBeCalled();
        $file->generate()->willReturn('code');

        $file->getClass()->willReturn($class);
        $file->setClass($class)->shouldBeCalled();
        $client = new Client(
            new ClientConfig('MyClient', new Destination('', 'MyNamespace')),
            new ClientMethodMap([$method])
        );
        $this->generate($file, $client)->shouldReturn('code');
    }
}
