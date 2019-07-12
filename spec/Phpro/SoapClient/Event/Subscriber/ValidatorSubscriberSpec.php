<?php

namespace spec\Phpro\SoapClient\Event\Subscriber;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Event\Subscriber\ValidatorSubscriber;
use Phpro\SoapClient\Exception\RequestException;
use Phpro\SoapClient\Type\RequestInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorSubscriberSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator)
    {
        $this->beConstructedWith($validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ValidatorSubscriber::class);
    }

    function it_should_be_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_throws_exception_on_invalid_requests(
        ValidatorInterface $validator,
        Client $client,
        RequestInterface $request,
        ConstraintViolation $violation1,
        ConstraintViolation $violation2
    ) {
        $event = new RequestEvent($client->getWrappedObject(), 'method', $request->getWrappedObject());
        $violation1->getMessage()->willReturn('error 1');
        $violation2->getMessage()->willReturn('error 2');
        $validator->validate($request)->willReturn(new ConstraintViolationList([
            $violation1->getWrappedObject(),
            $violation2->getWrappedObject(),
        ]));
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
