<?php

namespace spec\Phpro\SoapClient\Soap\Driver\ExtSoap\Generator;

use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Generator\DummyMethodArgumentsGenerator;

/**
 * Class DummyMethodArgumentsGeneratorSpec
 */
class DummyMethodArgumentsGeneratorSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata)
    {
        $this->beConstructedWith($metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DummyMethodArgumentsGenerator::class);
    }

    function it_can_parse_dummy_arguments(MetadataInterface $metadata)
    {
        $metadata->getMethods()->willReturn(
            new MethodCollection(
                new Method(
                    'method',
                    [
                        new Parameter('param1', XsdType::create('string')),
                        new Parameter('param1', XsdType::create('integer')),
                    ],
                    XsdType::create('string')
                )
            )
        );

        $this->generateForSoapCall('method')->shouldBe([null, null]);
    }
}
