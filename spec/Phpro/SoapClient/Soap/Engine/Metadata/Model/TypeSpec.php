<?php

namespace spec\Phpro\SoapClient\Soap\Engine\Metadata\Model;

use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;

/**
 * Class TypeSpec
 */
class TypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(XsdType::create('name'), [
            new Property('name', XsdType::create('type'))
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Type::class);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldBe('name');
    }

    function it_contains_a_xsd_type()
    {
        $this->getXsdType()->shouldBeLike(XsdType::create('name'));
    }

    function it_contains_properties()
    {
        $properties = $this->getProperties();
        $properties->shouldHaveCount(1);
        $properties[0]->shouldHaveType(Property::class);
        $properties[0]->getName()->shouldBe('name');
        $properties[0]->getType()->shouldBeLike(XsdType::create('type'));
    }
}
