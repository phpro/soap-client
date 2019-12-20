<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\EncoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Xml\SoapXml;
use PhproTest\SoapClient\Integration\Soap\Type\ValidateRequest;

abstract class AbstractEncoderTest extends AbstractIntegrationTest
{
    const XML_XSI_NS = 'http://www.w3.org/2001/XMLSchema-instance';

    abstract protected function getEncoder(): EncoderInterface;

    /** @test */
    public function it_handles_simple_content()
    {
        $this->configureForWsdl(FIXTURE_DIR.'/wsdl/functional/simpleContent.wsdl');
        $input = ['_' => 132, 'country' => 'BE'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals(132, $result->nodeValue);
        $this->assertEquals('BE', $result->getAttribute('country'));
    }

    /** @test */
    public function it_handles_complex_types()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/complex-type-request-response.wsdl');
        $input = (object)['input' => 'inputContent'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/request/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input->input, $result->nodeValue);
    }

    /** @test */
    public function it_handles_complex_types_with_classmap()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/complex-type-mapped-request-response.wsdl');
        $input = new ValidateRequest();
        $input->input = 'inputContent';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/request/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input->input, $result->nodeValue);
    }

    /** @test */
    public function it_handles_enum_types()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/enum.wsdl');
        $input = 'Home';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    public function it_handles_xml_entities()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/string.wsdl');
        $input = '<\'"ïnpüt"\'>';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertStringContainsString(htmlspecialchars($input, ENT_NOQUOTES), $encoded->getRequest());
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_null()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $input = null;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_string()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $input = 'hello';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_long()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $input = 132323;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_double()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $input = 23.22;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_false()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $input = false;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals('false', $result->nodeValue);
    }

    /** @test */
    function it_encodes_true()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/guess.wsdl');
        $input = true;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals('true', $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_string()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/string.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', ['input']);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals('input', $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_boolean()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/boolean.wsdl');
        $input = true;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals('true', $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_decimal()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/decimal.wsdl');
        $input = 10.4;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/*[1]');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_float()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/float.wsdl');
        $input = 10.4;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_double()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/double.wsdl');
        $input = 10.4;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/*[1]');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_long()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/long.wsdl');
        $input = 2323232323;
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_int()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/string.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_short()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/short.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_byte()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/byte.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 1]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_nonpositive_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/nonPositiveInteger.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = -123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_positive_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/positiveInteger.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_nonnegative_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/nonNegativeInteger.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_negative_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/negativeInteger.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_unsigned_byte()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/unsignedByte.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_unsigned_short()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/unsignedShort.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_unsigned_int()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/unsignedInt.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_unsigned_long()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/unsignedLong.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_integer()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/integer.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 123]);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_datetime()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/datetime.wsdl');
        $input = new \DateTimeImmutable('2019-01-25 11:30:00');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/*[1]');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input->format('Y-m-d\TH:i:sP'), $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_time()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/time.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = '11:30']);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_date()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/date.wsdl');
        $input = new \DateTimeImmutable('2019-01-25');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/*[1]');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input->format('Y-m-d'), $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_gyearmonth()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gYearMonth.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = '2019-01']);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_gyear()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gYear.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = '2019']);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_gmonthday()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gYearMonth.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = '--01-25']);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_gday()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gDay.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = '---25']);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_gmonth()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/gMonth.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = '--01']);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_duration()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/duration.wsdl');

        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 'PT2M10S']);
        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_hexbinary()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/hexBinary.wsdl');
        $input = 'myinput';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals(strtoupper(bin2hex($input)), $result->nodeValue);
    }

    /** @test */
    function it_encodes_base64binary()
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
    function it_encodes_xsd_any_type_by_guessing()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/any.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 12243.223]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_any_uri()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/anyURI.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 'http://www.w3.org/TR/xmlschema-0/']);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_qname()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/qname.wsdl');
        $input = 'xsd:someElement';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_notation()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/notation.wsdl');
        $input = 'xsd:NOTATION';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_normalized_string()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/normalizedString.wsdl');
        $input = ' Being a Dog Is 
 a Full-Time Job';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_token()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/token.wsdl');
        $input = ' Being a Dog Is 
 a Full-Time Job';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_language()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/language.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 'nl-BE']);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_nmtoken()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/nmtoken.wsdl');
        $input = 'noSpaces-Or-SpecialChars-allowed-1234';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_nmtokens()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/nmtokens.wsdl');
        $input = 'token-1 token-2 token-3';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_name()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/name.wsdl');
        $input = 'Cannot-start-with-number-134';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_ncname()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/ncname.wsdl');
        $input = 'Cannot-contain-colon-134';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_id()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/id.wsdl');
        $input = 'IDField';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_idref()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/idref.wsdl');
        $input = 'IDField';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_idrefs()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/idrefs.wsdl');
        $input = 'IDField1 IDField2';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_entity()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/entity.wsdl');
        $input = 'Entity';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_entities()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/entities.wsdl');
        $input = 'Entity';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_soap_11_enc_object()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/soap11-enc-object.wsdl');
        $input = (object) ['Sku' => 50, 'Description' => 'Description'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $this->assertSoapRequest($encoded, $xml, $method);

        $sku = $this->runSingleElementXpathOnBody($xml, './application:validate/request/Sku');
        $this->assertEquals($input->Sku, $sku->nodeValue);
        $this->assertEquals('xsd:int', $sku->getAttributeNS(self::XML_XSI_NS, 'type'));

        $description = $this->runSingleElementXpathOnBody($xml, './application:validate/request/Description');
        $this->assertEquals($input->Description, $description->nodeValue);
        $this->assertEquals('xsd:string', $description->getAttributeNS(self::XML_XSI_NS, 'type'));
    }

    /** @test */
    function it_encodes_soap_11_enc_array()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/soap11-enc-array.wsdl');
        $input = ['item1', 'item2'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $this->assertSoapRequest($encoded, $xml, $method);

        $item1 = $this->runSingleElementXpathOnBody($xml, './application:validate/request/item[1]');
        $this->assertEquals($input[0], $item1->nodeValue);
        $this->assertEquals('xsd:string', $item1->getAttributeNS(self::XML_XSI_NS, 'type'));

        $item2 = $this->runSingleElementXpathOnBody($xml, './application:validate/request/item[2]');
        $this->assertEquals($input[1], $item2->nodeValue);
        $this->assertEquals('xsd:string', $item2->getAttributeNS(self::XML_XSI_NS, 'type'));
    }

    /** @test */
    function it_encodes_soap_12_enc_object()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/soap12-enc-object.wsdl');
        $input = (object) ['Sku' => 50, 'Description' => 'Description'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $this->assertSoapRequest($encoded, $xml, $method);

        $sku = $this->runSingleElementXpathOnBody($xml, './application:validate/request/Sku');
        $this->assertEquals($input->Sku, $sku->nodeValue);
        $this->assertEquals('xsd:int', $sku->getAttributeNS(self::XML_XSI_NS, 'type'));

        $description = $this->runSingleElementXpathOnBody($xml, './application:validate/request/Description');
        $this->assertEquals($input->Description, $description->nodeValue);
        $this->assertEquals('xsd:string', $description->getAttributeNS(self::XML_XSI_NS, 'type'));
    }

    /** @test */
    function it_encodes_soap_12_enc_array()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/soap12-enc-array.wsdl');
        $input = ['item1', 'item2'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $this->assertSoapRequest($encoded, $xml, $method);

        $item1 = $this->runSingleElementXpathOnBody($xml, './application:validate/request/item[1]');
        $this->assertEquals($input[0], $item1->nodeValue);
        $this->assertEquals('xsd:string', $item1->getAttributeNS(self::XML_XSI_NS, 'type'));

        $item2 = $this->runSingleElementXpathOnBody($xml, './application:validate/request/item[2]');
        $this->assertEquals($input[1], $item2->nodeValue);
        $this->assertEquals('xsd:string', $item2->getAttributeNS(self::XML_XSI_NS, 'type'));
    }

    /** @test */
    function it_encodes_apache_map_array()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/apache-map.wsdl');
        $input = ['key1' => 'item1'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $this->assertSoapRequest($encoded, $xml, $method);

        $requestItem = $this->runSingleElementXpathOnBody($xml, './application:validate/request');
        $this->assertStringContainsString(':Map', $requestItem->getAttributeNS(self::XML_XSI_NS, 'type'));

        $item1Key = $this->runSingleElementXpathOnBody($xml, './application:validate/request/item[1]/key');
        $this->assertEquals('key1', $item1Key->nodeValue);
        $this->assertEquals('xsd:string', $item1Key->getAttributeNS(self::XML_XSI_NS, 'type'));

        $item1Value = $this->runSingleElementXpathOnBody($xml, './application:validate/request/item[1]/value');
        $this->assertEquals($input['key1'], $item1Value->nodeValue);
        $this->assertEquals('xsd:string', $item1Value->getAttributeNS(self::XML_XSI_NS, 'type'));
    }

    /** @test */
    function it_encodes_xsd_1999_string()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999string.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 'string']);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_boolean()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999boolean.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = true]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals('true', $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_decimal()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999decimal.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 10.23]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_float()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999float.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 10.23]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_double()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999double.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 10.23]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_long()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999long.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 1023]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_int()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999int.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 1023]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_short()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999short.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 1023]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_byte()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999byte.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = 1]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
    }

    /** @test */
    function it_encodes_xsd_1999_timeinstant()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/1999timeinstant.wsdl');
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input = '20190125T083100.001']);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals($input, $result->nodeValue);
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
