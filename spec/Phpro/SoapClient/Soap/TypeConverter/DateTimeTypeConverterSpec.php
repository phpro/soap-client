<?php

namespace spec\Phpro\SoapClient\Soap\TypeConverter;

use Phpro\SoapClient\Soap\TypeConverter\DateTimeTypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateTimeTypeConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateTimeTypeConverter::class);
    }

    function it_is_a_type_converter()
    {
        $this->shouldImplement(TypeConverterInterface::class);
    }
}
