<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Soap\TypeConverter;

use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Soap\TypeConverter\DateTypeConverter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeConverterCollectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([new DateTypeConverter()]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\SoapClient\Soap\TypeConverter\TypeConverterCollection');
    }

    function it_should_not_be_able_to_add_the_same_converter_twice()
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringAdd(new DateTypeConverter());
    }

    function it_should_know_its_registered_converters()
    {
        $this->has(new DateTypeConverter())->shouldBe(true);
    }

    function it_should_convert_to_soap_type_map()
    {
        $result = $this->toSoapTypeMap();
        $result[0]['type_name']->shouldBe('date');
    }
}
