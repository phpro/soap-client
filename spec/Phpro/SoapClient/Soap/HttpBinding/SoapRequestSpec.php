<?php

namespace spec\Phpro\SoapClient\Soap\HttpBinding;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;

/**
 * Class SoapRequestSpec
 */
class SoapRequestSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('requestbody', 'location', 'action', 1, 0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SoapRequest::class);
    }

    function it_contains_the_request_body()
    {
        $this->getRequest()->shouldBe('requestbody');
    }

    function it_contains_the_request_location()
    {
        $this->getLocation()->shouldBe('location');
    }

    function it_contains_an_action()
    {
        $this->getAction()->shouldBe('action');
    }

    function it_contains_a_version()
    {
        $this->getVersion()->shouldBe(1);
    }

    function it_knows_if_its_a_one_way_binding()
    {
        $this->getOneWay()->shouldBe(0);
    }
}
