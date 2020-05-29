<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class ClassMapGeneratorSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator
 * @mixin ClassMapGenerator
 */
class ClassMapGeneratorSpec extends ObjectBehavior
{
    function let(RuleSetInterface $ruleSet)
    {
        $this->beConstructedWith($ruleSet, 'ClassMap', 'App\\Mynamespace');
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType(ClassMapGenerator::class);
    }
    
    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_generates_classmaps(RuleSetInterface $ruleSet, FileGenerator $file, TypeMap $typeMap)
    {
        $ruleSet->applyRules(Argument::type(ClassMapContext::class))->shouldBeCalled();
        $file->generate()->willReturn('code');
        $this->generate($file, $typeMap)->shouldReturn('code');
    }
}
