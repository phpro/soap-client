<?php

namespace spec\Phpro\SoapClient\Soap\ClassMap;

use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClassMapSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('WsdlType', 'PhpClass');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClassMap::class);
    }

    function it_should_have_a_wsdl_type()
    {
        $this->getWsdlType()->shouldBe('WsdlType');
    }

    function it_should_have_a_php_classname()
    {
        $this->getPhpClassName()->shouldBe('PhpClass');
    }
}
