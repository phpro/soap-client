<?php

namespace spec\Phpro\SoapClient\Soap\HttpBinding;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Soap\SoapClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;

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
        $request = new Request('POST', '/', ['x-request-header' => 'value'], 'REQUESTBODY');
        $response = new Response(200, ['x-response-header' => 'value'], 'RESPONSEBODY');

        $result = $this->createFromPsr7RequestAndResponse($request, $response);
        $result->getLastRequestHeaders()->shouldBe('x-request-header: value');
        $result->getLastRequest()->shouldBe('REQUESTBODY');
        $result->getLastResponseHeaders()->shouldBe('x-response-header: value');
        $result->getLastResponse()->shouldBe('RESPONSEBODY');
    }

    function it_can_load_from_psr7_request_and_response_without_body()
    {
        $request = new Request('GET', '/', ['x-request-header' => 'value'], '');
        $respone = new Response(204, ['x-response-header' => 'value'], '');

        $result = $this->createFromPsr7RequestAndResponse($request, $respone);
        $result->getLastRequestHeaders()->shouldBe('x-request-header: value');
        $result->getLastRequest()->shouldBe('');
        $result->getLastResponseHeaders()->shouldBe('x-response-header: value');
        $result->getLastResponse()->shouldBe('');
    }
}
