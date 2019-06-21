<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Assembler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\Assembler\ImmutableSetterAssemblerOptions;

/**
 * Class ImmutableSetterAssemblerOptionsSpec
 */
class ImmutableSetterAssemblerOptionsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ImmutableSetterAssemblerOptions::class);
    }

    function it_should_create_options()
    {
        $this::create()->shouldBeAnInstanceOf(ImmutableSetterAssemblerOptions::class);
    }

    function it_should_have_false_as_default()
    {
        $options = $this::create();
        $options->useTypeHints()->shouldBe(false);
    }

    function it_should_set_type_hints()
    {
        $options = $this::create()->withTypeHints();
        $options->useTypeHints()->shouldBe(true);
    }

    function it_shout_set_return_types()
    {
        $options = $this::create()->withReturnTypes();
        $options->useReturnTypes()->shouldBe(true);
    }
}
