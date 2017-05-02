<?php

namespace spec\Phpro\SoapClient\Exception;

use Phpro\SoapClient\Exception\RuntimeException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Exception\WsdlException;

class WsdlExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WsdlException::class);
    }

    function it_should_be_an_exception()
    {
        $this->shouldHaveType(RuntimeException::class);
    }
}
