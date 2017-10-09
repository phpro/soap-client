<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Soap;

use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\SoapClient;
use Phpro\SoapClient\Soap\SoapClientFactory;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SoapClientFactorySpec extends ObjectBehavior
{
    function let(ClassMapCollection $classMap, TypeConverterCollection $typeConverters)
    {
        $classMap->toSoapClassMap()->willReturn([]);
        $typeConverters->toSoapTypeMap()->willReturn([]);
        $this->beConstructedWith($classMap, $typeConverters);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SoapClientFactory::class);
    }

}
