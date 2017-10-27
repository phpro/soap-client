<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Soap\ClassMap;

use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClassMapCollectionSpec extends ObjectBehavior
{
    function let(ClassMap $classMap)
    {
        $classMap->getWsdlType()->willReturn('WsdlType');
        $classMap->getPhpClassName()->willReturn('PhpClass');
        $this->beConstructedWith([$classMap]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClassMapCollection::class);
    }

    function it_knows_the_attached_classmaps(ClassMap $classMap)
    {
        $this->has($classMap)->shouldBe(true);
    }

    function it_should_not_be_able_to_add_the_same_classmap_twice(ClassMap $classMap)
    {
        $this->shouldThrow(InvalidArgumentException::class)->duringAdd($classMap);
    }

    function it_should_add_a_classmap(ClassMap $classMap2)
    {
        $this->has($classMap2)->shouldBe(false);
        $this->add($classMap2);
        $this->has($classMap2)->shouldBe(true);
    }

    function it_should_convert_to_wsdl_classmap()
    {
        $this->toSoapClassMap()->shouldBe([
            'WsdlType' => 'PhpClass'
        ]);
    }
}
