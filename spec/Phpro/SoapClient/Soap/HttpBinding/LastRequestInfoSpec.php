<?php

namespace spec\Phpro\SoapClient\Soap\HttpBinding;

use Phpro\SoapClient\Soap\SoapClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

/**
 * Class LastRequestInfoSpec
 */
class LastRequestInfoSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('requestheaders', 'request', 'responseheaders', 'response');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LastRequestInfo::class);
    }

    function it_contains_the_request_headers()
    {
        $this->getLastRequestHeaders()->shouldBe('requestheaders');
    }

    function it_contains_the_request()
    {
        $this->getLastRequest()->shouldBe('request');
    }

    function it_contains_the_response_headers()
    {
        $this->getLastResponseHeaders()->shouldBe('responseheaders');
    }

    function it_contains_the_response()
    {
        $this->getLastResponse()->shouldBe('response');
    }

    function it_can_create_an_empty_class()
    {
        $result = $this->createEmpty();
        $result->shouldBeAnInstanceOf(LastRequestInfo::class);
        $result->getLastRequestHeaders()->shouldBe('');
        $result->getLastRequest()->shouldBe('');
        $result->getLastResponseHeaders()->shouldBe('');
        $result->getLastResponse()->shouldBe('');
    }

    // Note: the __get* cannot be mocked with phpspec.
    function it_can_create_from_a_soapclient(SoapClient $client)
    {
        $result = $this->createFromSoapClient($client);
        $result->shouldBeAnInstanceOf(LastRequestInfo::class);
        $result->getLastRequestHeaders()->shouldBe('');
        $result->getLastRequest()->shouldBe('');
        $result->getLastResponseHeaders()->shouldBe('');
        $result->getLastResponse()->shouldBe('');
    }

    function it_can_load_from_psr7_request_and_response()
    {
        $request = new Request('/', 'POST', 'php://temp', ['x-request-header' => 'value']);
        $request->getBody()->write('REQUESTBODY');
        $response = new Response('php://memory', 200, ['x-response-header' => 'value']);
        $response->getBody()->write('RESPONSEBODY');

        $result = $this->createFromPsr7RequestAndResponse($request, $response);
        $result->getLastRequestHeaders()->shouldBe('X-Request-Header: value');
        $result->getLastRequest()->shouldBe('REQUESTBODY');
        $result->getLastResponseHeaders()->shouldBe('X-Response-Header: value');
        $result->getLastResponse()->shouldBe('RESPONSEBODY');
    }

    function it_can_load_from_psr7_request_and_response_without_body()
    {
        $request = new Request('/', 'GET', 'php://temp', ['x-request-header' => 'value']);
        $respone = new Response('php://memory', 204, ['x-response-header' => 'value']);

        $result = $this->createFromPsr7RequestAndResponse($request, $respone);
        $result->getLastRequestHeaders()->shouldBe('X-Request-Header: value');
        $result->getLastRequest()->shouldBe('');
        $result->getLastResponseHeaders()->shouldBe('X-Response-Header: value');
        $result->getLastResponse()->shouldBe('');
    }
}
