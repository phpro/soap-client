<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TypeSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin Type
 */
class TypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('MyNamespace', 'myType', ['prop1' => 'string']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Type::class);
    }

    function it_has_a_namespace()
    {
        $this->getNamespace()->shouldReturn('MyNamespace');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('MyType');
    }

    function it_has_a_xsd_type()
    {
        $this->getXsdName()->shouldReturn('myType');
    }

    function it_has_a_full_name()
    {
        $this->getFullName()->shouldReturn('MyNamespace\\MyType');
    }

    function it_has_a_path_name()
    {
        $this->getPathname('my/dir')->shouldReturn('my/dir/MyType.php');
    }

    function it_should_replace_underscores_in_paths()
    {
        $this->beConstructedWith('MyNamespace', 'myType_3_2', ['prop1' => 'string']);
        $this->getFileInfo('my/some_dir')->getPathname()->shouldReturn('my/some_dir/MyType/3/2.php');
    }

    function it_has_properties()
    {
        $props = $this->getProperties();
        $props[0]->shouldReturnAnInstanceOf(Property::class);
        $props[0]->getName()->shouldReturn('prop1');
        $props[0]->getType()->shouldReturn('string');
    }
}
