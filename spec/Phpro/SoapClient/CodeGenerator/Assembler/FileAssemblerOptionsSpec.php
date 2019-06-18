<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Assembler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\Assembler\FileAssemblerOptions;

/**
 * Class FileAssemblerOptionsSpec
 */
class FileAssemblerOptionsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FileAssemblerOptions::class);
    }

    function it_should_create_options()
    {
        $this::create()->shouldBeAnInstanceOf(FileAssemblerOptions::class);
    }

    function it_should_have_false_as_default()
    {
        $options = $this::create();
        $options->useStrictTypes()->shouldBe(false);
    }

    function it_should_set_strict_types()
    {
        $options = $this::create()->withStrictTypes();
        $options->useStrictTypes()->shouldBe(true);
    }
}
