<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Exception;

use Phpro\SoapClient\Exception\RuntimeException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Exception\RequestException;

class RequestExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RequestException::class);
    }

    function it_should_be_an_exception()
    {
        $this->shouldHaveType(RuntimeException::class);
    }
}
