<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Type;

use Phpro\SoapClient\Type\MultiArgumentRequest;
use Phpro\SoapClient\Type\MultiArgumentRequestInterface;
use Phpro\SoapClient\Type\RequestInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class MultiArgumentRequestSpec
 *
 * @package spec\Phpro\SoapClient\Type
 * @mixin MultiArgumentRequest
 */
class MultiArgumentRequestSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['arg1', 'arg2']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MultiArgumentRequest::class);
    }

    function it_is_a_multiple_argument_request()
    {
        $this->shouldImplement(MultiArgumentRequestInterface::class);
        $this->shouldImplement(RequestInterface::class);
    }

    function it_has_multiple_arguments()
    {
        $this->getArguments()->shouldBe(['arg1', 'arg2']);
    }
}
