<?php

namespace spec\Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\Assert;
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
        $abusedClient = $this->mockAbusedClient($methods = [
            'TestResponse Test0Param()',
            'TestResponse Test1Param(Test1 $parameter1)',
            'TestResponse Test2Param(Test1 $parameter1, Test2 $parameter2)',
            'list(Response1 $response1, Response2 $response2) TestReturnList()',
            'list(Response1 $response1, Response2 $response2) TestReturnListWithParams(Test1 $parameter1, Test2 $parameter2)',
        ]);

        $result = $this->parse($abusedClient);
        $result->shouldHaveType(MethodCollection::class);
        $result->shouldHaveCount(\count($methods));

        $result->fetchByName('Test0Param')->shouldHaveMethod(
            'Test0Param',
            [],
            'TestResponse'
        );
        $result->fetchByName('Test1Param')->shouldHaveMethod(
            'Test1Param',
            [
                new Parameter('parameter1', 'Test1'),
            ],
            'TestResponse'
        );
        $result->fetchByName('Test2Param')->shouldHaveMethod(
            'Test2Param',
            [
                new Parameter('parameter1', 'Test1'),
                new Parameter('parameter2', 'Test2'),
            ],
            'TestResponse'
        );
        $result->fetchByName('TestReturnList')->shouldHaveMethod(
            'TestReturnList',
            [],
            'array'
        );
        $result->fetchByName('TestReturnListWithParams')->shouldHaveMethod(
            'TestReturnListWithParams',
            [
                new Parameter('parameter1', 'Test1'),
                new Parameter('parameter2', 'Test2'),
            ],
            'array'
        );
    }

    public function getMatchers(): array
    {
        return [
            'haveMethod' => function (Method $subject, string $name, array $parameters, string $returnType) {
                $prefix = 'Expected method '.$name;

                Assert::assertInstanceOf(Method::class, $subject, $prefix.' is not of type Method');
                Assert::assertSame($name, $subject->getName(), $prefix. ' has unexpected name '.$subject->getName());
                Assert::assertEquals($parameters, $subject->getParameters(), $prefix. ' has invalid parameters.');
                Assert::assertSame($returnType, $subject->getReturnType(), $prefix. ' has unexpected return type.'.$subject->getReturnType());

                return true;
            },
        ];
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
