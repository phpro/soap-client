<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;

/**
 * Class ClientFactoryContextSpec
 */
class ClientFactoryContextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Myclient', 'App\\Client', 'Myclassmap', 'App\\Classmap');
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

    function it_constructs_from_config(Config $config)
    {
        $config->getClientNamespace()->willReturn('App\\Client');
        $config->getClientName()->willReturn($client = 'Someclient');
        $config->getClassMapNamespace()->willReturn('App\\Classmap');
        $config->getClassMapName()->willReturn('Myclassmap');
        $this->beConstructedThrough([ClientFactoryContext::class, 'fromConfig'], [$config]);
        $this->shouldHaveType(ClientFactoryContext::class);
        $this->getClientName()->shouldBe($client);
    }
}
