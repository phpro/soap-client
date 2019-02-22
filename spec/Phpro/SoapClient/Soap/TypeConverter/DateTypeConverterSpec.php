<?php

namespace spec\Phpro\SoapClient\Soap\TypeConverter;

use DateTime;
use Phpro\SoapClient\Soap\TypeConverter\DateTypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterInterface;
use PhpSpec\ObjectBehavior;
use stdClass;

class DateTypeConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateTypeConverter::class);
    }

    function it_is_a_type_converter()
    {
        $this->shouldImplement(TypeConverterInterface::class);
    }

    function it_creates_datetime_interface_from_xml()
    {
        $date = '2019-01-25';

        $result = $this->convertXmlToPhp('<date>'.$date.'</date>');
        $result->shouldBeAnInstanceOf(\DateTimeImmutable::class);
        $result->format('Y-m-d')->shouldBe($date);
    }

    function it_returns_empty_string_on_null_passed()
    {
        $this->convertPhpToXml(null)->shouldReturn('');
    }

    function it_returns_empty_string_on_wrong_type_passed()
    {
        $this->convertPhpToXml(new stdClass())->shouldReturn('');
    }

    function it_returns_correct_date_string_on_valid_type_passed()
    {
        $dateTime = new DateTime();
        $this->convertPhpToXml($dateTime)->shouldReturn('<date>' . $dateTime->format('Y-m-d') . '</date>');
    }
}
