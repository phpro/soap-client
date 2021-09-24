<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Metadata\Detector;

use Phpro\SoapClient\Soap\Metadata\Detector\ResponseTypesDetector;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\ParameterCollection;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use Soap\Engine\Metadata\Model\XsdType;

class ResponseTypesDetectorTest extends TestCase
{
    /** @test */
    public function it_can_detect_request_types(): void
    {
        $methods = new MethodCollection(
            new Method('method1', new ParameterCollection(), XsdType::create('Response1')),
            new Method('method3', new ParameterCollection(
                new Parameter('param1', XsdType::create('RequestType2')),
                new Parameter('param2', XsdType::create('RequestType3'))
            ), XsdType::create('Response2')),
            new Method('method1', new ParameterCollection(), XsdType::create('string'))
        );

        $detected = (new ResponseTypesDetector())($methods);
        self::assertSame(
            ['Response1', 'Response2', 'string'],
            $detected
        );
    }
}
