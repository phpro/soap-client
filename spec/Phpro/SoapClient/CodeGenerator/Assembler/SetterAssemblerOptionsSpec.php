<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Assembler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\Assembler\SetterAssemblerOptions;

/**
 * Class SetterAssemblerOptionsSpec
 */
class SetterAssemblerOptionsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SetterAssemblerOptions::class);
    }

    function it_should_create_options()
    {
        $this::create()->shouldBeAnInstanceOf(SetterAssemblerOptions::class);
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
}
