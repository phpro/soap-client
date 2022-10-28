<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Laminas\Code\Generator\FileGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\FileContext;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FileContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin FileContext
 */
class FileContextSpec extends ObjectBehavior
{
    function let(FileGenerator $file)
    {
        $this->beConstructedWith($file);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FileContext::class);
    }

    function it_is_a_context()
    {
        $this->shouldImplement(ContextInterface::class);
    }

    function it_has_a_file_generator(FileGenerator $file)
    {
        $this->getFileGenerator()->shouldReturn($file);
    }
}
