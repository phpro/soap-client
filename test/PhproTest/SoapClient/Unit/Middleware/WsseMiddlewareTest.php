<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Middleware\WsseMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Xml\SoapXml;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Class WsseMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class WsseMiddlewareTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var MockHandler
     */
    private $handler;

    /**
     * @var WsseMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp()
    {
        $this->handler = new MockHandler([]);
        $this->middleware = new WsseMiddleware(
            FIXTURE_DIR . '/certificates/wsse-client-private-key.pem',
            FIXTURE_DIR . '/certificates/wsse-client-public-key.pub'
        );
        $stack = new HandlerStack($this->handler);
        $stack->push($this->middleware);
        $this->client = new Client(['handler' => $stack]);
    }

    /**
     * @test
     */
    function it_is_a_middleware()
    {
        $this->assertInstanceOf(MiddlewareInterface::class, $this->middleware);
    }

    /**
     * @test
     */
    function it_has_a_name()
    {
        $this->assertEquals('wsse_middleware', $this->middleware->getName());
    }

    /**
     * @test
     */
    function it_adds_Wsse_to_the_request_xml()
    {
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request.xml');
        $this->handler->append($response = new Response(200));
        $result = $this->client->send($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        $this->assertEquals($result, $response);

        // Check request structure:
        $this->assertEquals($xml->xpath('//soap:Header/wsse:Security')->length, 1, 'No WSSE Security tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:BinarySecurityToken')->length, 1, 'No  WSSE BinarySecurityToken tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature')->length, 1, 'No DS Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo')->length, 1, 'No DS SignedInfo Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:CanonicalizationMethod')->length, 1, 'No DS SignedInfo CanonicalizationMethod Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:SignatureMethod')->length, 1, 'No DS SignedInfo SignatureMethod Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference')->length, 1, 'No DS SignedInfo Reference Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference/ds:Transforms/ds:Transform')->length, 1, 'No DS SignedInfo Reference Transform Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestMethod')->length, 1, 'No DS SignedInfo Reference DigestMethod Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestValue')->length, 1, 'No DS SignedInfo Reference DigestValue Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignatureValue')->length, 1, 'No DS SignatureValue Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:KeyInfo')->length, 1, 'No DS KeyInfo Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:KeyInfo/wsse:SecurityTokenReference/wsse:Reference')->length, 1, 'No DS KeyInfo SecurityTokenReference Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsu:Timestamp')->length, 1, 'No WSU Timestamp tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsu:Timestamp/wsu:Created')->length, 1, 'No WSU Created Timestamp tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsu:Timestamp/wsu:Expires')->length, 1, 'No WSU Expires Timestamp tag');


        // Check defaults:
        $this->assertEquals(
            XMLSecurityKey::RSA_SHA1,
            (string) $xml->xpath('//ds:SignatureMethod')->item(0)->getAttribute('Algorithm')
        );
        $this->assertEquals(
            strtotime((string) $xml->xpath('//wsu:Created')->item(0)->nodeValue),
            strtotime((string) $xml->xpath('//wsu:Expires')->item(0)->nodeValue) - 3600
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_configure_expiry_ttl()
    {
        $this->middleware->withTimestamp(100);
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request.xml');
        $this->handler->append($response = new Response(200));
        $this->client->send($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        $this->assertEquals(
            strtotime((string) $xml->xpath('//wsu:Created')->item(0)->nodeValue),
            strtotime((string) $xml->xpath('//wsu:Expires')->item(0)->nodeValue) - 100
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_sign_all_headers()
    {
        $this->middleware->withAllHeadersSigned();
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/wsa.xml');
        $this->handler->append($response = new Response(200));
        $this->client->send($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        $this->assertEquals(5, $xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference')->length, 'Not all headers are signed!');
        $this->assertEquals(1, $xml->xpath('//wsa:Action[@wsu:Id]')->length, 'No signed WSA:Action.');
        $this->assertEquals(1, $xml->xpath('//wsa:To[@wsu:Id]')->length, 'No signed WSA:To.');
        $this->assertEquals(1, $xml->xpath('//wsa:MessageID[@wsu:Id]')->length, 'No signed WSA:MessageID.');
        $this->assertEquals(1, $xml->xpath('//wsa:ReplyTo[@wsu:Id]')->length, 'No signed WSA:ReplyTo.');
    }

    /**
     * @test
     */
    function it_is_possible_to_specify_another_digital_signature_method()
    {
        $this->middleware->withDigitalSignMethod(XMLSecurityKey::RSA_SHA256);
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request.xml');
        $this->handler->append($response = new Response(200));
        $this->client->send($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        // Check defaults:
        $this->assertEquals(
            XMLSecurityKey::RSA_SHA256,
            (string) $xml->xpath('//ds:SignatureMethod')->item(0)->getAttribute('Algorithm')
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_specify_a_user_token()
    {
        $this->middleware->withUserToken('username', 'password', false);
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request.xml');
        $this->handler->append($response = new Response(200));
        $this->client->send($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        // Check defaults:
        $this->assertEquals(2, $xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference')->length, 'UserToken not signed!');
        $this->assertEquals($xml->xpath('//soap:Header/wsse:Security/wsse:UsernameToken')->length, 1, 'No WSSE UsernameToken tag');
        $this->assertEquals(1, $xml->xpath('//wsse:Security/wsse:UsernameToken[@wsu:Id]')->length, 'UserToken not signed!');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:UsernameToken/wsse:Username')->length, 1, 'No WSSE UserName tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:UsernameToken/wsse:Password')->length, 1, 'No WSSE Password tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:UsernameToken/wsse:Nonce')->length, 1, 'No WSSE Nonce tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:UsernameToken/wsu:Created')->length, 1, 'No WSU Created tag');

        // Check values:
        $this->assertEquals('username', (string) $xml->xpath('//wsse:Username')->item(0)->nodeValue);
        $this->assertEquals('password', (string) $xml->xpath('//wsse:Password')->item(0)->nodeValue);
        $this->assertEquals(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText',
            (string) $xml->xpath('//wsse:Password')->item(0)->getAttribute('Type')
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_specify_a_user_token_with_digest()
    {
        $this->middleware->withUserToken('username', 'password', true);
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request.xml');
        $this->handler->append($response = new Response(200));
        $this->client->send($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        // Check defaults:
        $this->assertEquals($xml->xpath('//soap:Header/wsse:Security/wsse:UsernameToken')->length, 1, 'No WSSE UsernameToken tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:UsernameToken/wsse:Username')->length, 1, 'No WSSE UserName tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:UsernameToken/wsse:Password')->length, 1, 'No WSSE Password tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:UsernameToken/wsse:Nonce')->length, 1, 'No WSSE Nonce tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:UsernameToken/wsu:Created')->length, 1, 'No WSU Created tag');

        // Check values:
        $this->assertEquals('username', (string) $xml->xpath('//wsse:Username')->item(0)->nodeValue);
        $this->assertNotEquals('password', (string) $xml->xpath('//wsse:Password')->item(0)->nodeValue);
        $this->assertEquals(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest',
            (string) $xml->xpath('//wsse:Password')->item(0)->getAttribute('Type')
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_encrypt_a_request()
    {
        $this->markTestIncomplete(
            'Encryption is not possible in newer versions of PHP. More info: ' .
            'https://github.com/robrichards/wse-php/issues/28'
        );

        $this->middleware->withEncryption(FIXTURE_DIR . '/certificates/wsse-client-x509.pem');
        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request.xml');
        $this->handler->append($response = new Response(200));
        $this->client->send($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->handler->getLastRequest()->getBody();
        $xml = $this->fetchSoapXml($soapBody);


        $this->assertTrue(false, 'TODO: Add asserts!');
    }

    /**
     * @param $soapBody
     *
     * @return SoapXml
     */
    private function fetchSoapXml($soapBody): SoapXml
    {
        $xml = new \DOMDocument();
        $xml->loadXML($soapBody);

        $soapXml = new SoapXml($xml);
        $soapXml->registerNamespace('wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
        $soapXml->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
        $soapXml->registerNamespace('wsu', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');
        $soapXml->registerNamespace('wsa', 'http://schemas.xmlsoap.org/ws/2004/08/addressing');

        return $soapXml;
    }
}
