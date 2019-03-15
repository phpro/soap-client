<?php

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
        $this->beConstructedWith(
            $namespace = 'MyNamespace',
            'myType',
            [new Property('prop1', 'string', $namespace)]
        );
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

    function it_should_not_replace_underscores_in_paths()
    {
        $this->beConstructedWith('MyNamespace', 'my_type_3_2', ['prop1' => 'string']);
        $this->getFileInfo('my/some_dir')->getPathname()->shouldReturn('my/some_dir/MyType32.php');
    }

    function it_should_prefix_reserved_keywords()
    {
        $this->beConstructedWith(
            $namespace = 'MyNamespace',
            'Final',
            [new Property('xor', 'string', $namespace)]
        );

        $this->getFileInfo('my/some_dir')->getPathname()->shouldReturn('my/some_dir/FinalType.php');
        $this->getName()->shouldReturn('FinalType');
        $this->getProperties()[0]->getName()->shouldReturn('xor');
    }

    function it_has_properties()
    {
        $props = $this->getProperties();
        $props[0]->shouldReturnAnInstanceOf(Property::class);
        $props[0]->getName()->shouldReturn('prop1');
        $props[0]->getType()->shouldReturn('string');
    }
}
