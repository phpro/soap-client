<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use PhpSpec\ObjectBehavior;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;

/**
 * Class ConfigContextSpec
 */
class ConfigContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ConfigContext::class);
    }

    function it_is_a_context()
    {
        $this->shouldImplement(ContextInterface::class);
    }

    function it_has_type_namespace_fallback()
    {
        $this->setTypeDestination($destination = new Destination('src/type', 'App\\Type'));
        $this->getTypeDestination()->shouldBe($destination);
    }

    function it_has_client_config()
    {
        $clientConfig = new ClientConfig('MyClient', new Destination('src/client', 'App\\Client'));
        $this->setClientConfig($clientConfig);
        $this->getClientConfig()->shouldBe($clientConfig);
    }

    function it_has_classmap_config()
    {
        $classMapConfig = new ClassMapConfig('MyClassmap', new Destination('src/classmap', 'App\\Classmap'));
        $this->setClassMapConfig($classMapConfig);
        $this->getClassMapConfig()->shouldBe($classMapConfig);
    }
}
