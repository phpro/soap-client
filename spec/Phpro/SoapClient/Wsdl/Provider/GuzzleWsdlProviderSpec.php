<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Wsdl\Provider;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Exception\WsdlException;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Middleware\MiddlewareSupportingInterface;
use Phpro\SoapClient\Util\Filesystem;
use Phpro\SoapClient\Wsdl\Provider\GuzzleWsdlProvider;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GuzzleWsdlProviderSpec extends ObjectBehavior
{
    function let(ClientInterface $client, Filesystem $filesystem, HandlerStack $handlerStack)
    {
        $this->beConstructedWith($client, $filesystem);
        $client->getConfig('handler')->willReturn($handlerStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GuzzleWsdlProvider::class);
    }

    function it_is_a_wsdl_provider()
    {
        $this->shouldImplement(WsdlProviderInterface::class);
    }

    function it_supports_middlewares()
    {
        $this->shouldImplement(MiddlewareSupportingInterface::class);
    }

    function it_can_load_wsdl_from_url(ClientInterface $client, Filesystem $filesystem)
    {
        $filesystem->fileExists(Argument::any())->willReturn(true);
        $client->request('GET', $wsdl = 'some.wsdl')->willReturn(new Response(200, [], $body = 'wsdl'));

        $filesystem->putFileContents(Argument::any(), $body)->shouldBeCalled();

        $this->provide($wsdl)->shouldBeString();
    }

    function it_can_store_a_wsdl_to_a_specific_location(ClientInterface $client, Filesystem $filesystem)
    {
        $destination = 'destination.wsdl';
        $filesystem->fileExists($destination)->willReturn(true);
        $client->request('GET', $wsdl = 'some.wsdl')->willReturn(new Response(200, [], $body = 'wsdl'));

        $filesystem->putFileContents($destination, $body)->shouldBeCalled();

        $this->setLocation($destination);
        $this->provide($wsdl)->shouldBeString();
    }

    function it_throws_an_exception_if_the_destination_file_does_not_exist(Filesystem $filesystem)
    {
        $filesystem->fileExists(Argument::any())->willReturn(false);

        $this->shouldThrow(WsdlException::class)->duringProvide('some.wsdl');
    }

    function it_throws_an_exception_if_the_remote_file_does_not_exist(ClientInterface $client, Filesystem $filesystem)
    {
        $filesystem->fileExists(Argument::any())->willReturn(true);
        $client->request('GET', $wsdl = 'some.wsdl')->willThrow(new \Exception('invalid request'));
        $this->shouldThrow(WsdlException::class)->duringProvide('some.wsdl');
    }

    function it_can_handle_middlewares(ClientInterface $client, Filesystem $filesystem, HandlerStack $handlerStack, MiddlewareInterface $middleware)
    {
        $middleware->getName()->willReturn('middleware_name');
        $handlerStack->push($middleware, 'middleware_name')->shouldBeCalled();
        $handlerStack->remove($middleware)->shouldBeCalled();


        $destination = 'destination.wsdl';
        $filesystem->fileExists($destination)->willReturn(true);
        $client->request('GET', $wsdl = 'some.wsdl')->willReturn(new Response(200, [], $body = 'wsdl'));

        $filesystem->putFileContents($destination, $body)->shouldBeCalled();

        $this->addMiddleware($middleware);
        $this->setLocation($destination);
        $this->provide($wsdl)->shouldBeString();
    }
}
