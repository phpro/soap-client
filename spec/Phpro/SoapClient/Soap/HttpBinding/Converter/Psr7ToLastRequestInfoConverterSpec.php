<?php

namespace spec\Phpro\SoapClient\Soap\HttpBinding\Converter;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\Converter\Psr7ToLastRequestInfoConverter;

/**
 * Class Psr7ToLastRequestInfoConverterSpec
 */
class Psr7ToLastRequestInfoConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Psr7ToLastRequestInfoConverter::class);
    }


    function it_can_load_from_psr7_request_and_response()
    {
        $request = new Request('POST', '/', ['x-request-header' => 'value'], 'REQUESTBODY');
        $response = new Response(200, ['x-response-header' => 'value'], 'RESPONSEBODY');

        $result = $this->convert($request, $response);
        $result->shouldBeAnInstanceOf(LastRequestInfo::class);
        $result->getLastRequestHeaders()->shouldBe('x-request-header: value');
        $result->getLastRequest()->shouldBe('REQUESTBODY');
        $result->getLastResponseHeaders()->shouldBe('x-response-header: value');
        $result->getLastResponse()->shouldBe('RESPONSEBODY');
    }

    function it_can_load_from_psr7_request_and_response_without_body()
    {
        $request = new Request('GET', '/', ['x-request-header' => 'value'], '');
        $respone = new Response(204, ['x-response-header' => 'value'], '');

        $result = $this->convert($request, $respone);
        $result->shouldBeAnInstanceOf(LastRequestInfo::class);
        $result->getLastRequestHeaders()->shouldBe('x-request-header: value');
        $result->getLastRequest()->shouldBe('');
        $result->getLastResponseHeaders()->shouldBe('x-response-header: value');
        $result->getLastResponse()->shouldBe('');
    }
}
