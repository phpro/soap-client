<?php

namespace spec\Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\MethodsParser;

/**
 * Class MethodsParserSpec
 */
class MethodsParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MethodsParser::class);
    }

    function it_can_parse_ext_soap_function_strings()
    {
        $abusedClient = $this->mockAbusedClient([
            'TestResponse Test(Test1 $parameter1, Test2 $parameter2)',
        ]);

        $result = $this->parse($abusedClient);
        $result->shouldHaveType(MethodCollection::class);
        $result->shouldHaveCount(1);

        $method = $result->fetchByName('Test');
        $method->shouldHaveType(Method::class);
        $method->getName()->shouldBe('Test');
        $method->getReturnType()->shouldBe('TestResponse');

        $parameters = $method->getParameters();
        $parameters->shouldHaveCount(2);
        $parameters[0]->shouldHaveType(Parameter::class);
        $parameters[0]->getName()->shouldBe('parameter1');
        $parameters[0]->getType()->shouldBe('Test1');
        $parameters[1]->shouldHaveType(Parameter::class);
        $parameters[1]->getName()->shouldBe('parameter2');
        $parameters[1]->getType()->shouldBe('Test2');

    }

    /**
     * Phpspec cant mock the __getFunctions
     */
    private function mockAbusedClient(array $functions): AbusedClient
    {
        return new class($functions) extends AbusedClient {
            /** @var array */
            private $functions;

            public function __construct(array $functions)
            {
                $this->functions = $functions;
            }

            public function __getFunctions()
            {
                return $this->functions;
            }
        };
    }
}
