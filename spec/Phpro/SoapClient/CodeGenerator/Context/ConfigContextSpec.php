<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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

    function it_adds_setters()
    {
        $this->addSetter('setTest', 'test\'run');
        $this->getSetters()->shouldBeArray();
        $this->getSetters()['setTest']->shouldBe('test\'run');
    }
}
