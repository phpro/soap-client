<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TypeMapSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin TypeMap
 */
class TypeMapSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('MyNamespace', [
            'type1' => [
                'prop1' => 'string',
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeMap::class);
    }

    function it_has_a_namespace()
    {
        $this->getNamespace()->shouldReturn('MyNamespace');
    }

    function it_has_types()
    {
        $types = $this->getTypes();
        $types[0]->shouldReturnAnInstanceOf(Type::class);
        $types[0]->getXsdName()->shouldBe('type1');
    }
}
