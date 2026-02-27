<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\FileContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laminas\Code\Generator\FileGenerator;
use Soap\Engine\Metadata\Collection\TypeCollection;

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
        $classMapConfig = new ClassMapConfig('ClassMap', new Destination('/app', 'App\\Mynamespace'));
        $this->beConstructedWith($ruleSet, $classMapConfig);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType(ClassMapGenerator::class);
    }
    
    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_generates_classmaps(RuleSetInterface $ruleSet, FileGenerator $file)
    {
        $ruleSet->applyRules(Argument::type(ClassMapContext::class))->shouldBeCalled();
        $ruleSet->applyRules(Argument::type(FileContext::class))->shouldBeCalled();
        $file->generate()->willReturn('code');
        $namespaceMap = TypeNamespaceMap::create(new Destination('/app', 'App\\Mynamespace'));
        $codeGeneratorContext = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->generate($file, TypeMap::fromMetadata(
            $codeGeneratorContext,
            new TypeCollection(),
        ))->shouldReturn('code');
    }
}
