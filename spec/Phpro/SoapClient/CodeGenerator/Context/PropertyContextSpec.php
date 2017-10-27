<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class PropertyContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin PropertyContext
 */
class PropertyContextSpec extends ObjectBehavior
{
    function let(ClassGenerator $class, Type $type, Property $property)
    {
        $this->beConstructedWith($class, $type, $property);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType(PropertyContext::class);
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
    
    function it_has_a_property(Property $property)
    {
        $this->getProperty()->shouldReturn($property);
    }
}
