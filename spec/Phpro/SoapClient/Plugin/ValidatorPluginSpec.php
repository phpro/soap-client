<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Plugin;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Exception\RequestException;
use Phpro\SoapClient\Type\RequestInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Phpro\SoapClient\Plugin\ValidatorPlugin;

class ValidatorPluginSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator)
    {
        $this->beConstructedWith($validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ValidatorPlugin::class);
    }

    function it_should_be_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_throws_exception_on_invalid_requests(
        ValidatorInterface $validator,
        Client $client,
        RequestInterface $request,
        ConstraintViolation $violation
    ) {
        $event = new RequestEvent($client->getWrappedObject(), 'method', $request->getWrappedObject());
        $violation->__toString()->willReturn('error');
        $validator->validate($request)->willReturn(new ConstraintViolationList([$violation->getWrappedObject()]));
        $this->shouldThrow(RequestException::class)->duringOnClientRequest($event);
    }

    function it_does_not_throw_exception_onnvalid_requests(
        ValidatorInterface $validator,
        Client $client,
        RequestInterface $request
    ) {
        $event = new RequestEvent($client->getWrappedObject(), 'method', $request->getWrappedObject());
        $validator->validate($request)->willReturn(new ConstraintViolationList([]));
        $this->shouldNotThrow(RequestException::class)->duringOnClientRequest($event);
    }
}
