<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Metadata\Detector;

use Phpro\SoapClient\Soap\Metadata\Detector\RequestTypesDetector;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\ParameterCollection;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use Soap\Engine\Metadata\Model\XsdType;

class RequestTypesDetectorTest extends TestCase
{
    /** @test */
    public function it_can_detect_request_types(): void
    {
        $methods = new MethodCollection(
            new Method('method1', new ParameterCollection(), XsdType::create('string')),
            new Method('method2', new ParameterCollection(
                new Parameter('param1', XsdType::create('RequestType1'))
            ), XsdType::create('string')),
            new Method('method3', new ParameterCollection(
                new Parameter('param1', XsdType::create('RequestType2')),
                new Parameter('param2', XsdType::create('RequestType3'))
            ), XsdType::create('string')),
            new Method('method4', new ParameterCollection(
                new Parameter('param1', XsdType::create('RequestType4'))
            ), XsdType::create('string')),
            new Method('method5', new ParameterCollection(
                new Parameter('param1', XsdType::create('string'))
            ), XsdType::create('string'))
        );

        $detected = (new RequestTypesDetector())($methods);
        self::assertSame(
            ['RequestType1', 'RequestType4', 'string'],
            $detected
        );
    }
}
