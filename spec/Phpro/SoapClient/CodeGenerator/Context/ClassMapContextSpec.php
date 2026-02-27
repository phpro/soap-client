<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use PhpSpec\ObjectBehavior;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class ClassMapContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin ClassMapContext
 */
class ClassMapContextSpec extends ObjectBehavior
{
    private TypeMap $typeMap;

    function let(FileGenerator $fileGenerator)
    {
        $typeDestination = new Destination('src/type', 'App\\Mynamespace');
        $namespaceMap = TypeNamespaceMap::create($typeDestination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->typeMap = new TypeMap($context, []);

        $classMapDestination = new Destination('src/classmap', 'App\\Mynamespace');
        $classMapConfig = new ClassMapConfig('ClassMap', $classMapDestination);

        $this->beConstructedWith($fileGenerator, $this->typeMap, $classMapConfig, $context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClassMapContext::class);
    }

    function it_is_a_context()
    {
        $this->shouldImplement(ContextInterface::class);
    }

    function it_has_a_file_generator(FileGenerator $fileGenerator)
    {
        $this->getFile()->shouldReturn($fileGenerator);
    }

    function it_has_a_typemap()
    {
        $this->getTypeMap()->shouldReturn($this->typeMap);
    }
}
