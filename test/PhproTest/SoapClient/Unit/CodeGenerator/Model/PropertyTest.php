<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\Property;
use PHPUnit\Framework\TestCase;

/**
 * Class PropertyTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Model
 */
class PropertyTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_null_type_pre_php8(): void
    {
        if (PHP_VERSION_ID >= 80000) {
            self::markTestSkipped('Pre PHP 8 only');
        }
        $property = new Property('test', 'mixed', 'App');
        self::assertNull($property->getCodeReturnType());
        self::assertEquals('mixed', $property->getType());
    }

    /**
     * @test
     */
    public function it_returns_mixed_type_post_php8(): void
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped('Post PHP 8 only');
        }
        $property = new Property('test', 'mixed', 'App');
        self::assertEquals('mixed', $property->getCodeReturnType());
        self::assertEquals('mixed', $property->getType());
    }
}
