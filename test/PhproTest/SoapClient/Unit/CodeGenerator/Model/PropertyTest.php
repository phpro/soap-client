<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\Property;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

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
    public function it_returns_mixed_type_post_php8(): void
    {
        $property = new Property('test', 'mixed', 'App', new TypeMeta());
        self::assertEquals('mixed', $property->getPhpType());
        self::assertEquals('mixed', $property->getType());
    }
}
