<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\EncoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Xml\SoapXml;

abstract class AbstractEncoderTest extends AbstractIntegrationTest
{
    abstract protected function getEncoder(): EncoderInterface;

    /** @test */
    public function it_encodes_base64_binary()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/base64Binary.wsdl');
        $input = 'myinput';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals(base64_encode($input), $result->nodeValue);
    }

    /** @test */
    public function it_handles_simple_content()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/simpleContent.wsdl');
        $input = ['_' => 132, 'country' => 'BE'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals(132, $result->nodeValue);
        $this->assertEquals('BE', $result->getAttribute('country'));
    }

    /** @test */
    public function it_handles_xml_entities()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/stringContent.wsdl');
        $input = '<\'"ïnpüt"\'>';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertContains(htmlspecialchars($input, ENT_NOQUOTES), $encoded->getRequest());
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_unknwon_types_by_guessing()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_null()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_string()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_long()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_double()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_false()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_true()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_array_soap11()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_array_soap12()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_object_soap11()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_object_soap12()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_string()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_boolean()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_decimal()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_float()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_double()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_long()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_int()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_short()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_byte()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_nonpositive_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_positive_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_nonnegative_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_negative_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_unsigned_byte()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_unsigned_short()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_unsigned_int()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_unsigned_long()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_datetime()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_time()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_date()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_gyearmonth()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_gyear()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_gmonthday()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_gday()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_gmonth()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_duration()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_hexbinary()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_base64binary()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_any_type_by_guessing()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_ur_type_by_guessing()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_any_uri()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_qname()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_notation()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_normalized_string()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_token()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_language()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_nmtoken()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_nmtokens()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_name()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_ncname()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_id()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_idref()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_idrefs()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_entity()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_entities()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_apache_map()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_soap_11_enc_object()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_soap_11_enc_array()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_soap_12_enc_object()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_soap_12_enc_array()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_string()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_boolean()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_decimal()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_float()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_double()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_long()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_int()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_short()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_byte()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_encodes_xsd_1999_timeinstant()
    {
        $this->markTestIncomplete('TODO...');
    }

    /**
     * we make some assumptions in this method:
     * - Location = body namespace
     * - action = body namespace/Method
     * - No one way configured
     */
    protected function assertSoapRequest(SoapRequest $request, SoapXml $xml, string $method)
    {
        $bodyNamespace = $xml->detectBodyContentsNamespace();
        $this->assertEquals($bodyNamespace, $request->getLocation());
        $this->assertEquals(rtrim($bodyNamespace, '/').'/'.$method, $request->getAction());
        $this->assertTrue($request->isSOAP11() || $request->isSOAP12());
        $this->assertEquals(0, $request->getOneWay());
    }
}
