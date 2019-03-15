<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\DecoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use PhproTest\SoapClient\Integration\Soap\Type\ValidateResponse;

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
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/complex-type-request-response.wsdl');
        $output = 'hello';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <Response>
        <output>$output</output>
    </Response>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertInstanceOf(\stdClass::class, $decoded);
        $this->assertSame($output, $decoded->output);
    }

    /** @test */
    public function it_handles_complex_types_with_classmap()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/complex-type-mapped-request-response.wsdl');
        $output = 'hello';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <Response>
        <output>$output</output>
    </Response>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertInstanceOf(ValidateResponse::class, $decoded);
        $this->assertSame($output, $decoded->output);
    }

    /** @test */
    public function it_handles_enum_types()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/enum.wsdl');
        $output = 'Home';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="ns2:PhoneTypeEnum">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
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
        $this->assertSame($expectedOutput, $decoded);
    }

    /** @test */
    function it_decodes_null()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:nil="true" />
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame(null, $decoded);
    }

    /** @test */
    function it_decodes_string()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $output = 'string';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output>$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_long()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $output = 132;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:long">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_double()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $output = 132.12;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:double">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_false()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:boolean">false</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame(false, $decoded);
    }

    /** @test */
    function it_decodes_true()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:boolean">true</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame(true, $decoded);
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
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_boolean()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/boolean.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:boolean">true</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame(true, $decoded);
    }

    /** @test */
    function it_decodes_xsd_decimal()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/decimal.wsdl');
        $output = 12345.67890;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:decimal">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
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
        $this->assertSame($output, $decoded);
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
        $this->assertSame($output, $decoded);
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
        $this->assertSame($output, $decoded);
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
        $this->assertSame($output, $decoded);
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
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_byte()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/byte.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:byte">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_nonpositive_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/nonPositiveInteger.wsdl');
        $output = -123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:nonPositiveInteger">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_positive_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/positiveInteger.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:positiveInteger">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_nonnegative_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/nonNegativeInteger.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:nonNegativeInteger">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_negative_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/negativeInteger.wsdl');
        $output = -123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:negativeInteger">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_unsigned_byte()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/unsignedByte.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:unsignedByte">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_unsigned_short()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/unsignedShort.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:unsignedShort">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_unsigned_int()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/unsignedInt.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:unsignedInt">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_unsigned_long()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/unsignedLong.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:unsignedInt">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/integer.wsdl');
        $output = 123;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:integer">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_datetime()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/datetime.wsdl');
        $output = '2018-01-25T21:32:52';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:dateTime">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertInstanceOf(\DateTimeInterface::class, $decoded);
        $this->assertSame($output, $decoded->format('Y-m-d\TH:i:s'));
    }

    /** @test */
    function it_decodes_xsd_time()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/time.wsdl');
        $output = '21:32:52';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:time">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_date()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/date.wsdl');
        $output = '2019-01-25';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:date">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertInstanceOf(\DateTimeInterface::class, $decoded);
        $this->assertSame($output, $decoded->format('Y-m-d'));
    }

    /** @test */
    function it_decodes_xsd_gyearmonth()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gYearMonth.wsdl');
        $output = '2019-01';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:gYearMonth">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_gyear()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gYear.wsdl');
        $output = '2019';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:gYear">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_gmonthday()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gMonthDay.wsdl');
        $output = '--01-25';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:gMonthDay">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_gday()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gDay.wsdl');
        $output = '---25';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:gDay">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_gmonth()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gMonth.wsdl');
        $output = '--01';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:gMonth">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_duration()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/duration.wsdl');
        $output = 'PT2M10S';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:duration">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_hexbinary()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/hexBinary.wsdl');
        $output = bin2hex($expectedOutput = 'decodedoutput');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:hexBinary">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);

        $this->assertSame($expectedOutput, $decoded);
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

        $this->assertSame($expectedOutput, $decoded);
    }

    /** @test */
    function it_decodes_xsd_any_type()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/any.wsdl');
        $output = '12243.223';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:any">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_any_uri()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/anyURI.wsdl');
        $output = 'http://www.w3.org/TR/xmlschema-0/';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:anyURI">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_qname()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/qname.wsdl');
        $output = 'xsd:someElement';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:qname">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_notation()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/notation.wsdl');
        $output = 'xsd:NOTATION';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:notation">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_normalized_string()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/normalizedString.wsdl');
        $output = ' Being a Dog Is 
 a Full-Time Job';
        $expected = ' Being a Dog Is   a Full-Time Job';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:normalizedString">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($expected, $decoded);
    }

    /** @test */
    function it_decodes_xsd_token()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/token.wsdl');
        $output = '  Being a Dog Is 
  a Full-Time Job';
        $expected = 'Being a Dog Is a Full-Time Job';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:token">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($expected, $decoded);
    }

    /** @test */
    function it_decodes_xsd_language()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/token.wsdl');
        $output = 'nl-BE';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:language">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_nmtoken()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/nmtoken.wsdl');
        $output = 'noSpaces-Or-SpecialChars-allowed-1234';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:nmtoken">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_nmtokens()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/nmtokens.wsdl');
        $output = 'token-1 token-2 token-3';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:nmtokens">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_name()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/name.wsdl');
        $output = 'Cannot-start-with-number-134';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:name">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_ncname()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/ncname.wsdl');
        $output = 'Cannot-contain-colon-134';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:ncname">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_ncnames()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/ncnames.wsdl');
        $output = 'Cannot-contain-colon-134 ncname2';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:ncnames">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_id()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/id.wsdl');
        $output = 'IDField';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:ID">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_idref()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/idref.wsdl');
        $output = 'IDField';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:IDREF">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_idrefs()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/idrefs.wsdl');
        $output = 'IDField1 IDField2';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:IDREFS">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_entity()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/entity.wsdl');
        $output = 'Entity';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:entity">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_entities()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/entities.wsdl');
        $output = 'Entity1 Entity2';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:entities">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_soap_11_enc_object()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/soap11-enc-object.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output type="SOAP-ENC:Struct">
        <Sku xsi:type="xsd:int">50</Sku>
        <Description>Description</Description>
    </output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertInstanceOf(\stdClass::class, $decoded);
        $this->assertSame($decoded->Sku, 50);
        $this->assertSame($decoded->Description, 'Description');
    }

    /** @test */
    function it_decodes_soap_11_enc_array()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/soap11-enc-array.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output type="SOAP-ENC:array" SOAP-ENC:arrayType="string[]">
        <item>string1</item>
        <item>string2</item>
    </output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals(['string1', 'string2'], $decoded);
    }

    /** @test */
    function it_decodes_soap_12_enc_object()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/soap12-enc-object.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output type="enc:Struct">
        <Sku xsi:type="xsd:int">50</Sku>
        <Description>Description</Description>
    </output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertInstanceOf(\stdClass::class, $decoded);
        $this->assertSame($decoded->Sku, 50);
        $this->assertSame($decoded->Description, 'Description');
    }

    /** @test */
    function it_decodes_soap_12_enc_array()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/soap12-enc-array.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output type="enc:array" enc:arrayType="string[]">
        <item>string1</item>
        <item>string2</item>
    </output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals(['string1', 'string2'], $decoded);
    }

    /** @test */
    function it_decodes_apache_map_array()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/apache-map.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="apache:Map">
        <item>
            <key xsi:type="xsd:string">Key1</key>
            <value xsi:type="xsd:string">Value1</value>
        </item>
    </output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals(['Key1' => 'Value1'], $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_string()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999string.wsdl');
        $output = 'output';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:string">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_boolean()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999boolean.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:boolean">true</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame(true, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_decimal()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999decimal.wsdl');
        $output = 20.2;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:decimal">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame((string) $output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_float()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999float.wsdl');
        $output = 20.2;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:float">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_double()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999double.wsdl');
        $output = 20.2;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:double">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_long()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999long.wsdl');
        $output = 20;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:long">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_int()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999int.wsdl');
        $output = 20;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:int">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_short()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999short.wsdl');
        $output = 2;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:short">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_byte()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999byte.wsdl');
        $output = 1;
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:byte">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    /** @test */
    function it_decodes_xsd_1999_timeinstant()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999timeinstant.wsdl');
        $output = '20190125T083100.001';
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd1999:timeinstant">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertSame($output, $decoded);
    }

    protected function createResponse(string $applicationBodyXml): SoapResponse
    {
        return new SoapResponse(<<<EOXML
<SOAP-ENV:Envelope
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:application="http://soapinterop.org/"
    xmlns:s="http://soapinterop.org/xsd"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsd1999="http://www.w3.org/1999/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    xmlns:enc="http://www.w3.org/2003/05/soap-encoding"
    xmlns:apache="http://xml.apache.org/xml-soap"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        $applicationBodyXml
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOXML
        );
    }
}
