<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Soap\Handler;

use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use Phpro\SoapClient\Soap\SoapClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Handler\SoapHandle;

/**
 * Class SoapHandleSpec
 */
class SoapHandleSpec extends ObjectBehavior
{
    function let(SoapClient $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SoapHandle::class);
    }

    function it_is_a_soap_handler()
    {
        $this->shouldHaveType(HandlerInterface::class);
    }

    function it_can_request_soap_messages(SoapClient $client)
    {
        $request = new SoapRequest('body', 'uri', 'action', 1, 0);
        $client->doInternalRequest('body', 'uri', 'action', 1, 0)->willReturn($response = 'result');

        $result = $this->request($request);
        $result->shouldHaveType(SoapResponse::class);
        $result->getResponse()->shouldBe($response);
    }
}
