<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class ClientFactoryContextSpec
 */
class ClientFactoryContextSpec extends ObjectBehavior
{
    function let()
    {
        $clientContext = new ClientContext('Myclient', 'App\\Client');
        $classMapContext = new ClassMapContext(
            new FileGenerator(),
            new TypeMap('ns', []),
            'Myclassmap',
            'App\\Classmap'
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
