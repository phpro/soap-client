<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laminas\Code\Generator\ClassGenerator;

/**
 * Class TypeContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin TypeContext
 */
class TypeContextSpec extends ObjectBehavior
{
    function let(ClassGenerator $class, Type $type)
    {
        $this->beConstructedWith($class, $type);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeContext::class);
    }

    function it_is_a_context()
    {
        $this->shouldImplement(ContextInterface::class);
    }

    function it_has_a_class_generator(ClassGenerator $class)
    {
        $this->getClass()->shouldReturn($class);
    }

    function it_has_a_type(Type $type)
    {
        $this->getType()->shouldReturn($type);
    }
}
