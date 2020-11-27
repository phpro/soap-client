<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Laminas\Code\Generator\Exception\ClassNotFoundException;
use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\ClientGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\FileGenerator;

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

    function it_generates_clients(RuleSetInterface $ruleSet, FileGenerator $file, Client $client, ClientMethodMap $map, ClassGenerator $class)
    {
        $method = new ClientMethod('Test', [new Parameter('parameters', 'Test')], 'TestResponse');
        $ruleSet->applyRules(Argument::type(ClientMethodContext::class))->shouldBeCalled();
        $file->generate()->willReturn('code');

        $file->getClass()->willThrow(new ClassNotFoundException('No class is set'));
        $file->setClass(Argument::type(ClassGenerator::class))->shouldBeCalled();

        $client->getMethodMap()->willReturn($map);
        $map->getMethods()->willReturn([$method]);
        $client->getNamespace()->willReturn('MyNamespace');
        $client->getName()->willReturn('MyClient');
        $this->generate($file, $client)->shouldReturn('code');
    }

    private function assert_generates_clients_for_file_without_classes(RuleSetInterface $ruleSet, FileGenerator $file, Client $client, ClientMethodMap $map, ClassGenerator $class)
    {
        $method = new ClientMethod('Test', [new Parameter('parameters', 'Test')], 'TestResponse');
        $ruleSet->applyRules(Argument::type(ClientMethodContext::class))->shouldBeCalled();
        $file->generate()->willReturn('code');

        $file->getClass()->willReturn($class);
        $file->setClass($class)->shouldBeCalled();

        $client->getMethodMap()->willReturn($map);
        $map->getMethods()->willReturn([$method]);
        $client->getNamespace()->willReturn('MyNamespace');
        $client->getName()->willReturn('MyClient');
        $this->generate($file, $client)->shouldReturn('code');
    }
}
