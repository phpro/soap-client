<?php

namespace PhproTest\SoapClient\Unit\Middleware;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\PluginClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Mock\Client;
use Phpro\SoapClient\Middleware\WsseMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Xml\SoapXml;
use PHPUnit\Framework\TestCase;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Class WsseMiddleware
 *
 * @package PhproTest\SoapClient\Unit\Middleware
 */
class WsseMiddlewareTest extends TestCase
{
    /**
     * @var PluginClient
     */
    private $client;

    /**
     * @var Client
     */
    private $mockClient;

    /**
     * @var WsseMiddleware
     */
    private $middleware;

    /***
     * Initialize all basic objects
     */
    protected function setUp(): void
    {
        $this->middleware = new WsseMiddleware(
            FIXTURE_DIR . '/certificates/wsse-client-private-key.pem',
            FIXTURE_DIR . '/certificates/wsse-client-public-key.pub'
        );
        $this->mockClient = new Client(new GuzzleMessageFactory());
        $this->client = new PluginClient($this->mockClient, [$this->middleware]);
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
        $this->mockClient->addResponse($response = new Response(200));
        $result = $this->client->sendRequest($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->mockClient->getRequests()[0]->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        $this->assertEquals($result, $response);

        // Check request structure:
        $this->assertEquals($xml->xpath('//soap:Header/wsse:Security')->length, 1, 'No WSSE Security tag');
        $this->assertEquals($xml->xpath('//wsse:Security/wsse:BinarySecurityToken')->length, 1, 'No  WSSE BinarySecurityToken tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature')->length, 1, 'No DS Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo')->length, 1, 'No DS SignedInfo Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:CanonicalizationMethod')->length, 1, 'No DS SignedInfo CanonicalizationMethod Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:SignatureMethod')->length, 1, 'No DS SignedInfo SignatureMethod Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference')->length, 2, 'No DS SignedInfo Reference Signature tags');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference/ds:Transforms/ds:Transform')->length, 2, 'No DS SignedInfo Reference Transform Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestMethod')->length, 2, 'No DS SignedInfo Reference DigestMethod Signature tag');
        $this->assertEquals($xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestValue')->length, 2, 'No DS SignedInfo Reference DigestValue Signature tag');
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
        $this->mockClient->addResponse($response = new Response(200));
        $this->client->sendRequest($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->mockClient->getRequests()[0]->getBody();
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
        $this->mockClient->addResponse($response = new Response(200));
        $this->client->sendRequest($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->mockClient->getRequests()[0]->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        $this->assertEquals(6, $xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference')->length, 'Not all headers are signed!');
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
        $this->mockClient->addResponse($response = new Response(200));
        $this->client->sendRequest($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->mockClient->getRequests()[0]->getBody();
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
        $this->mockClient->addResponse($response = new Response(200));
        $this->client->sendRequest($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->mockClient->getRequests()[0]->getBody();
        $xml = $this->fetchSoapXml($soapBody);

        // Check defaults:
        $this->assertEquals(3, $xml->xpath('//wsse:Security/ds:Signature/ds:SignedInfo/ds:Reference')->length, 'UserToken not signed!');
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
        $this->mockClient->addResponse($response = new Response(200));
        $this->client->sendRequest($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $soapBody = (string)$this->mockClient->getRequests()[0]->getBody();
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
        $this->middleware->withEncryption(FIXTURE_DIR . '/certificates/wsse-client-x509.pem');
        $this->middleware->withServerCertificateHasSubjectKeyIdentifier(true);

        $soapRequest = file_get_contents(FIXTURE_DIR . '/soap/empty-request-with-head-and-body.xml');
        $soapResponse = file_get_contents(FIXTURE_DIR . '/soap/wsse-decrypt-response.xml');
        $this->mockClient->addResponse($response = new Response(200, [], $soapResponse));
        $response = $this->client->sendRequest($request = new Request('POST', '/', ['SOAPAction' => 'myaction'], $soapRequest));

        $encryptedXml = $this->fetchSoapXml((string)$this->mockClient->getRequests()[0]->getBody());
        $decryptedXml = $this->fetchSoapXml($response->getBody());

        // Check Request headers:
        $this->assertEquals($encryptedXml->xpath('//soap:Header/wsse:Security/xenc:EncryptedKey')->length, 1, 'No EncryptedKey tag');
        $this->assertEquals($encryptedXml->xpath('//soap:Header/wsse:Security/xenc:EncryptedKey/xenc:EncryptionMethod')->length, 1, 'No EncryptionMethod tag');
        $this->assertEquals($encryptedXml->xpath('//soap:Header/wsse:Security/xenc:EncryptedKey/dsig:KeyInfo')->length, 1, 'No KeyInfo tag');
        $this->assertEquals($encryptedXml->xpath('//soap:Header/wsse:Security/xenc:EncryptedKey/dsig:KeyInfo/wsse:SecurityTokenReference')->length, 1, 'No SecurityTokenReference tag');
        $this->assertEquals($encryptedXml->xpath('//soap:Header/wsse:Security/xenc:EncryptedKey/dsig:KeyInfo/wsse:SecurityTokenReference/wsse:KeyIdentifier')->length, 1, 'No KeyIdentifier tag');
        $this->assertEquals($encryptedXml->xpath('//soap:Header/wsse:Security/ds:Signature')->length, 0, 'Signature is not encrypted');
        $this->assertEquals($encryptedXml->xpath('//soap:Header/wsse:Security/xenc:EncryptedData')->length, 1, 'Signature is not encrypted');

        // Check request body:
        $this->assertEquals($encryptedXml->xpath('//soap:Body/xenc:EncryptedData')->length, 1, 'No EncryptedData tag');
        $this->assertEquals($encryptedXml->xpath('//soap:Body/xenc:EncryptedData/xenc:EncryptionMethod')->length, 1, 'No EncryptionMethod tag');
        $this->assertEquals($encryptedXml->xpath('//soap:Body/xenc:EncryptedData/xenc:CipherData')->length, 1, 'No CipherData tag');
        $this->assertEquals($encryptedXml->xpath('//soap:Body/xenc:EncryptedData/xenc:CipherData/xenc:CipherValue')->length, 1, 'No CipherValue tag');

        // Check response headers:
        $this->assertEquals($decryptedXml->xpath('//soap:Header/wsse:Security/xenc:EncryptedData')->length, 0, 'Encrypted data was not decrypted');
        $this->assertEquals($decryptedXml->xpath('//soap:Header/wsse:Security/ds:Signature')->length, 1, 'Signature could not be decrypted');

        // Check respone body:
        $this->assertEquals($decryptedXml->xpath('//soap:Body/xenc:EncryptedData')->length, 0, 'Encrypted data was not decrypted');
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
        $soapXml->registerNamespace('xenc', 'http://www.w3.org/2001/04/xmlenc#');
        $soapXml->registerNamespace('dsig', 'http://www.w3.org/2000/09/xmldsig#');

        return $soapXml;
    }
}
