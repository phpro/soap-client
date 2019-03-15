<?php

namespace spec\Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\MethodCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Method;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\Assert;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\MethodsParser;

/**
 * Class MethodsParserSpec
 */
class MethodsParserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new XsdTypeCollection(
            XsdType::create('simpleType')
                ->withBaseType('string')
        ));
    }

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
            'simpleType TestSimpleType(simpleType $parameter1)',
        ]);

        $result = $this->parse($abusedClient);
        $result->shouldHaveType(MethodCollection::class);
        $result->shouldHaveCount(\count($methods));

        $result->fetchOneByName('Test0Param')->shouldHaveMethod(
            'Test0Param',
            [],
            XsdType::create('TestResponse')
        );
        $result->fetchOneByName('Test1Param')->shouldHaveMethod(
            'Test1Param',
            [
                new Parameter('parameter1', XsdType::create('Test1')),
            ],
            XsdType::create('TestResponse')
        );
        $result->fetchOneByName('Test2Param')->shouldHaveMethod(
            'Test2Param',
            [
                new Parameter('parameter1', XsdType::create('Test1')),
                new Parameter('parameter2', XsdType::create('Test2')),
            ],
            XsdType::create('TestResponse')
        );
        $result->fetchOneByName('TestReturnList')->shouldHaveMethod(
            'TestReturnList',
            [],
            XsdType::create('array')
        );
        $result->fetchOneByName('TestReturnListWithParams')->shouldHaveMethod(
            'TestReturnListWithParams',
            [
                new Parameter('parameter1', XsdType::create('Test1')),
                new Parameter('parameter2', XsdType::create('Test2')),
            ],
            XsdType::create('array')
        );

        $simpleType = XsdType::create('simpleType')->withBaseType('string');
        $result->fetchOneByName('TestSimpleType')->shouldHaveMethod(
            'TestSimpleType',
            [
                new Parameter('parameter1', $simpleType)
            ],
            $simpleType
        );
    }

    public function getMatchers(): array
    {
        return [
            'haveMethod' => function (Method $subject, string $name, array $parameters, XsdType $returnType) {
                $prefix = 'Expected method '.$name;

                Assert::assertInstanceOf(Method::class, $subject, $prefix.' is not of type Method');
                Assert::assertSame($name, $subject->getName(), $prefix. ' has unexpected name '.$subject->getName());
                Assert::assertEquals($parameters, $subject->getParameters(), $prefix. ' has invalid parameters.');
                Assert::assertEquals($returnType, $subject->getReturnType(), $prefix. ' has unexpected return type.'.$subject->getReturnType()->getName());

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
