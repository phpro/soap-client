<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Parser;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\Parser\FunctionStringParser;
use Zend\Code\Generator\ParameterGenerator;

/**
 * Class FunctionStringParserSpec
 * @mixin FunctionStringParser
 */
class FunctionStringParserSpec extends ObjectBehavior
{
    function let() {
        $this->beConstructedWith('TestResponse Test(Test1 $parameter1, Test2 $parameter2)', 'MyParameterNamespace');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FunctionStringParser::class);
    }

    function it_should_parse_parameters() {
        $result = $this->parseParameters();
        $result->shouldHaveCount(2);

        $result[0]->shouldHaveType(ParameterGenerator::class);
        $result[0]->getName()->shouldBe('parameter1');
        $result[0]->getType()->shouldBe('MyParameterNamespace\Test1');

        $result[1]->shouldHaveType(ParameterGenerator::class);
        $result[1]->getName()->shouldBe('parameter2');
        $result[1]->getType()->shouldBe('MyParameterNamespace\Test2');
    }

    function it_should_parse_name() {
        $this->parseName()->shouldReturn('Test');
    }

    function it_should_parse_return_type() {
        $this->parseReturnType()->shouldReturn('TestResponse');
    }
}
