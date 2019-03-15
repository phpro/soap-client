<?php

namespace spec\Phpro\SoapClient\Exception;

use Phpro\SoapClient\Exception\RuntimeException;
use Phpro\SoapClient\Exception\SoapException;
use PhpSpec\ObjectBehavior;

class SoapExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SoapException::class);
    }

    function it_should_be_an_exception()
    {
        $this->shouldHaveType(RuntimeException::class);
    }

    function it_should_handle_non_int_codes()
    {
        $e = new class ('message') extends \Exception
        {
            protected $code = 'HY000';
        };
        $this->beConstructedThrough('fromThrowable', [$e]);
        $this->getCode()->shouldBe(0);
    }
}
