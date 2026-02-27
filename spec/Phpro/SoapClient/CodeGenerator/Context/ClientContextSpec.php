<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use PhpSpec\ObjectBehavior;

/**
 * Class ClientContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin ClientContext
 */
class ClientContextSpec extends ObjectBehavior
{
    function let(ClassGenerator $class)
    {
        $destination = new Destination('src/client', 'App\Client');
        $clientConfig = new ClientConfig('MyClient', $destination);
        $this->beConstructedWith($class, $clientConfig);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientContext::class);
    }

    function is_has_a_name()
    {
        $this->getName()->shouldBe('MyClient');
    }

    function it_has_a_namespace()
    {
        $this->getNamespace()->shouldBe('App\Client');
    }

    function it_has_a_class(ClassGenerator $class)
    {
        $this->getClass()->shouldBe($class);
    }
}
