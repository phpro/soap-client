<?php

namespace spec\Phpro\SoapClient\Soap\TypeConverter;

use DateTime;
use Phpro\SoapClient\Soap\TypeConverter\DateTimeTypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterInterface;
use PhpSpec\ObjectBehavior;
use stdClass;

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

    function it_returns_empty_string_on_null_passed()
    {
        $this->convertPhpToXml(null)->shouldReturn('');
    }

    function it_returns_empty_string_on_wrong_type_passed()
    {
        $this->convertPhpToXml(new stdClass())->shouldReturn('');
    }

    function it_returns_correct_datetime_string_on_valid_type_passed()
    {
        $dateTime = new DateTime();
        $this->convertPhpToXml($dateTime)->shouldReturn(
            '<dateTime>' . $dateTime->format('Y-m-d\TH:i:sP') . '</dateTime>'
        );
    }
}
