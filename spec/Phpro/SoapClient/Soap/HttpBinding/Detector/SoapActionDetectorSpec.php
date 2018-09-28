<?php

namespace spec\Phpro\SoapClient\Soap\HttpBinding\Detector;

use Http\Client\Exception\RequestException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\Detector\SoapActionDetector;
use Psr\Http\Message\RequestInterface;

class SoapActionDetectorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SoapActionDetector::class);
    }

    function it_can_detect_soap_action_from_soap_11_SOAPAction_header(RequestInterface $request)
    {
        $request->getHeader('SOAPAction')->willReturn([
            0 => 'actionhere',
        ]);
        $this->detectFromRequest($request)->shouldBe('actionhere');
    }

    function it_can_detect_soap_action_from_soap_12_content_type_header_with_double_quote(RequestInterface $request)
    {
        $request->getHeader('SOAPAction')->willReturn([]);
        $request->getHeader('Content-Type')->willReturn([
            0 => 'application/soap+xml;charset=UTF-8;action="actionhere"'
        ]);
        $this->detectFromRequest($request)->shouldBe('actionhere');
    }

    function it_can_detect_soap_action_from_soap_12_content_type_header_with_single_quote(RequestInterface $request)
    {
        $request->getHeader('SOAPAction')->willReturn([]);
        $request->getHeader('Content-Type')->willReturn([
            0 => 'application/soap+xml;charset=UTF-8;action=\'actionhere\''
        ]);
        $this->detectFromRequest($request)->shouldBe('actionhere');
    }

    function it_throws_an_http_request_exception_when_no_header_could_be_found(RequestInterface $request)
    {
        $request->getHeader('SOAPAction')->willReturn([]);
        $request->getHeader('Content-Type')->willReturn([]);
        $this->shouldThrow(RequestException::class)->duringDetectFromRequest($request);
    }
}
