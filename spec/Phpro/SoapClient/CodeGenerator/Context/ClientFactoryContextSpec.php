<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use PhpSpec\ObjectBehavior;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class ClientFactoryContextSpec
 */
class ClientFactoryContextSpec extends ObjectBehavior
{
    function let()
    {
        $clientDestination = new Destination('src/client', 'App\\Client');
        $clientConfig = new ClientConfig('Myclient', $clientDestination);
        $clientContext = new ClientContext(new ClassGenerator(), $clientConfig);

        $classMapDestination = new Destination('src/classmap', 'App\\Classmap');
        $typeDestination = new Destination('src/type', 'ns');
        $namespaceMap = \Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap::create($typeDestination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $classMapConfig = new ClassMapConfig('Myclassmap', $classMapDestination);
        $classMapContext = new ClassMapContext(
            new FileGenerator(),
            new TypeMap($context, []),
            $classMapConfig,
            $context
        );
        $this->beConstructedWith($clientContext, $classMapContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientFactoryContext::class);
    }

    function it_is_a_context()
    {
        $this->shouldImplement(ContextInterface::class);
    }

    function it_returns_client_fqcn()
    {
        $this->getClientFqcn()->shouldBe('App\\Client\\Myclient');
    }

    function it_returns_classmap_fqcn()
    {
        $this->getClassmapFqcn()->shouldBe('App\\Classmap\\Myclassmap');
    }

    function it_returns_a_client_name()
    {
        $this->getClientName()->shouldBe('Myclient');
    }

    function it_returns_the_client_namespace()
    {
        $this->getClientNamespace()->shouldBe('App\\Client');
    }

    function it_returns_the_classmap_name()
    {
        $this->getClassmapName()->shouldBe('Myclassmap');
    }

    function it_returns_the_classmap_namespace()
    {
        $this->getClassmapNamespace()->shouldBe('App\\Classmap');
    }
}
