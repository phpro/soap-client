<?php
require './vendor/autoload.php';

$client = new \Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient('test/fixtures/wsdl/functional/date.wsdl', [
    'typemap' => [
        [
            'type_name' => 'date',
            'type_ns' => 'http://www.w3.org/2001/XMLSchema',
            'from_xml' => function ()  {
                return 'hello';
            },
            'to_xml' => function ()  {
                return '<d>hello</d>';
            },
        ]
    ]
]);

$response = new \Phpro\SoapClient\Soap\HttpBinding\SoapResponse(<<<EOXML
<SOAP-ENV:Envelope
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:application="http://soapinterop.org/"
    xmlns:s="http://soapinterop.org/xsd"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        <application:validate>
            <output xsi:type="xsd:date">2018-01-01</output>
        </application:validate>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOXML
);


// This works...
$client->registerResponse($response);
var_dump($client->__soapCall('validate', [1]));

// This dont ... : the decoder needs the exact amount of parameters in the __soapCall to make it work...
$decoder = new \Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDecoder($client);
var_dump($decoder->decode('validate', $response));