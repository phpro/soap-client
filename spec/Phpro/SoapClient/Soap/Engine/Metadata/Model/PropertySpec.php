<?php

namespace spec\Phpro\SoapClient\Soap\Engine\Metadata\Model;

use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;

/**
 * Class PropertySpec
 */
class PropertySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('name', XsdType::create('type'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Property::class);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldBe('name');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldBeLike(XsdType::create('type'));
    }
}
