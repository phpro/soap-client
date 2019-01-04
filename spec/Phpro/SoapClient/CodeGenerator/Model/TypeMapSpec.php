<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TypeMapSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin TypeMap
 */
class TypeMapSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('MyNamespace', [
            [
                'typeName' => 'type1',
                'properties' => [
                    'prop1' => 'string',
                ],
                'duplicate' => false
            ]
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeMap::class);
    }

    function it_has_a_namespace()
    {
        $this->getNamespace()->shouldReturn('MyNamespace');
    }

    function it_has_types()
    {
        $types = $this->getTypes();
        $types[0]->shouldReturnAnInstanceOf(Type::class);
        $types[0]->getXsdName()->shouldBe('type1');
    }
}
