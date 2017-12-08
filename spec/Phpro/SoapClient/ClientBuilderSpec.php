<?php

namespace spec\Phpro\SoapClient;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\ClientBuilder;
use Phpro\SoapClient\ClientFactory;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\SoapClientFactory;
use Phpro\SoapClient\Soap\TypeConverter\DateTimeTypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\DateTypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\DecimalTypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\DoubleTypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterCollection;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterInterface;
use PhpSpec\ObjectBehavior;

class ClientBuilderSpec extends ObjectBehavior
{
    function let()
    {
        $wsdl = realpath(__DIR__ . '/../../../test/fixtures/wsdl/wheater-ws.wsdl');
        $this->beConstructedWith(new ClientFactory(Client::class), $wsdl, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientBuilder::class);
    }

    function it_should_load_a_new_client()
    {
        $this->build()->shouldBeAnInstanceOf(Client::class);
    }

    function it_should_add_default_converters_to_client()
    {
        $this->createSoapClientFactory()->shouldBeLike(
            new SoapClientFactory(new ClassMapCollection(), new TypeConverterCollection([
                new DateTimeTypeConverter(),
                new DateTypeConverter(),
                new DecimalTypeConverter(),
                new DoubleTypeConverter()
            ])));

        $this->build()->shouldBeAnInstanceOf(Client::class);
    }

    function it_should_has_option_to_override_default_converters(
        DateTimeTypeConverter $myDateTimeTypeConverter
    ) {
        $myDateTimeTypeConverter
            ->getTypeNamespace()
            ->willReturn('http://www.w3.org/2001/XMLSchema');
        $myDateTimeTypeConverter
            ->getTypeName()
            ->willReturn('dateTime');

        $this->shouldNotThrow(\InvalidArgumentException::class);
        $this->addTypeConverter($myDateTimeTypeConverter);

        $this->createSoapClientFactory()->shouldBeLike(
            new SoapClientFactory(new ClassMapCollection(), new TypeConverterCollection([
                $myDateTimeTypeConverter->getWrappedObject(),
                new DateTypeConverter(),
                new DecimalTypeConverter(),
                new DoubleTypeConverter()
            ])));

        $this->build()->shouldBeAnInstanceOf(Client::class);
    }
}