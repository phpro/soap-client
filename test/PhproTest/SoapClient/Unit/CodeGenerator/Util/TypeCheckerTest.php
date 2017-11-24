<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Util;

use Phpro\SoapClient\CodeGenerator\Util\TypeChecker;
use PHPUnit\Framework\TestCase;

/**
 * Class TypeCheckerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Util
 */
class TypeCheckerTest extends TestCase
{
    /**
     * @test
     * @dataProvider typeProvider
     */
    function it_can_check_a_type($type, $expected)
    {
        $this->assertEquals($expected, TypeChecker::isKnownType($type));
    }

    /**
     * @return array
     */
    function typeProvider()
    {
        return [
            ['void', true],
            ['int', true],
            ['float', true],
            ['string', true],
            ['bool', true],
            ['array', true],
            ['callable', true],
            ['iterable', true],
            ['Unknown class', false],
        ];
    }
}
