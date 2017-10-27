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
