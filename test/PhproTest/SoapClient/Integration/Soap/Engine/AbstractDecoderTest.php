<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\DecoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

abstract class AbstractDecoderTest extends AbstractIntegrationTest
{
    abstract protected function getDecoder(): DecoderInterface;

    /** @test */
    public function it_handles_simple_content()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/simpleContent.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="s:SimpleContent" country="BE">132</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals(
            (object)([
                '_' => 132,
                'country' => 'BE',
            ]),
            $decoded
        );
    }

    /** @test */
    public function it_handles_complex_types()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    public function it_handles_xml_entities()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/string.wsdl');
        $output = htmlspecialchars($expectedOutput = '&lt;\'"ïnpüt"\'&gt;', ENT_NOQUOTES);
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:string">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($expectedOutput, $decoded);
    }

    /** @test */
    function it_decodes_unknwon_types_by_guessing()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_null()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_string()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_enum()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_long()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_double()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_false()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_true()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_array_soap11()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_array_soap12()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_object_soap11()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_object_soap12()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_string()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/string.wsdl');
        $output = 'output';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:string">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_boolean()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_decimal()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/decimal.wsdl');
        $output = '12345.67890';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:decimal">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_float()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/float.wsdl');
        $output = 123.45;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:float">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_double()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/double.wsdl');
        $output = 123.45;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:double">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_long()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/long.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:long">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_int()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/int.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:int">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_short()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/short.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:int">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_byte()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_nonpositive_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_positive_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_nonnegative_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_negative_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_unsigned_byte()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_unsigned_short()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_unsigned_int()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_unsigned_long()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_integer()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_datetime()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_time()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_date()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_gyearmonth()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_gyear()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_gmonthday()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_gday()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_gmonth()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_duration()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_hexbinary()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_base64binary()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/base64Binary.wsdl');
        $output = base64_encode($expectedOutput = 'decodedoutput');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:base64Binary">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);

        $this->assertEquals($expectedOutput, $decoded);
    }


    /** @test */
    function it_decodes_xsd_any_type_by_guessing()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_ur_type_by_guessing()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_any_uri()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_qname()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_notation()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_normalized_string()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_token()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_language()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_nmtoken()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_nmtokens()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_name()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_ncname()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_id()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_idref()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_idrefs()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_entity()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_entities()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_apache_map()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_soap_11_enc_object()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_soap_11_enc_array()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_soap_12_enc_object()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_soap_12_enc_array()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_string()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_boolean()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_decimal()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_float()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_double()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_long()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_int()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_short()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_byte()
    {
        $this->markTestIncomplete('TODO...');
    }

    /** @test */
    function it_decodes_xsd_1999_timeinstant()
    {
        $this->markTestIncomplete('TODO...');
    }

    protected function createResponse(string $applicationBodyXml): SoapResponse
    {
        return new SoapResponse(<<<EOXML
<SOAP-ENV:Envelope
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:application="http://soapinterop.org/"
    xmlns:s="http://soapinterop.org/xsd"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        $applicationBodyXml
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOXML
        );
    }
}
