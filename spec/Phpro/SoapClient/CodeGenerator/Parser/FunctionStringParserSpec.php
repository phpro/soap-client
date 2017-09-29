<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Parser;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\Parser\FunctionStringParser;

/**
 * Class FunctionStringParserSpec
 * @mixin FunctionStringParser
 */
class FunctionStringParserSpec extends ObjectBehavior
{
    function let() {
        $this->beConstructedWith('TestResponse Test(Test $parameters)', 'MyParameterNamespace');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FunctionStringParser::class);
    }

    function it_should_parse_parameters() {
        $this->parseParameters()->shouldHaveCount(1);
    }

    function it_should_parse_name() {
        $this->parseName()->shouldReturn('Test');
    }

    function it_should_parse_return_type() {
        $this->parseReturnType()->shouldReturn('TestResponse');
    }
}
