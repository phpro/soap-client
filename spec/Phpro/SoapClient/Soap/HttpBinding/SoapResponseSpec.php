<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Soap\HttpBinding;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

/**
 * Class SoapResponseSpec
 */
class SoapResponseSpec extends ObjectBehavior
{
    private  $response = '<soap:Envelope />';

    function let()
    {
        $this->beConstructedWith($this->response);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SoapResponse::class);
    }

    function it_contains_the_response()
    {
        $this->getResponse()->shouldBe($this->response);
    }
}
