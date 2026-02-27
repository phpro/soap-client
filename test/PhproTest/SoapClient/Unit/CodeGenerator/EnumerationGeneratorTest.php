<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\EnumerationGenerator;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\FileGenerator;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

class EnumerationGeneratorTest extends TestCase
{
    use ConfigurationHelper;
    public function testStringBackedEnumGeneration(): void
    {
        $type = new Type(
            $this->createCodeGeneratorContext('MyNamespace'),
            'MyType',
            'MyType',
            [],
            XsdType::create('MyType')
                ->withBaseType('string')
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withIsSimple(true)
                        ->withEnums(['', 'Home', 'Office', 'Gsm'])
                )
        );

        $expected = <<<CODE
        <?php
        
        namespace MyNamespace;
        
        enum MyType: string {
            case Empty = '';
            case Home = 'Home';
            case Office = 'Office';
            case Gsm = 'Gsm';
        }
        
        CODE;


        $generator = new EnumerationGenerator();
        $generated = $generator->generate(new FileGenerator(), $type);
        self::assertEquals($expected, $generated);
    }

    public function testIntBackedEnumGeneration(): void
    {
        $type = new Type(
            $this->createCodeGeneratorContext('MyNamespace'),
            'MyType',
            'MyType',
            [],
            XsdType::create('MyType')
                ->withBaseType('integer')
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withIsSimple(true)
                        ->withEnums(['0', '1', '2'])
                )
        );

        $expected = <<<CODE
        <?php
        
        namespace MyNamespace;
        
        enum MyType: int {
            case Value_0 = 0;
            case Value_1 = 1;
            case Value_2 = 2;
        }
        
        CODE;

        $generator = new EnumerationGenerator();
        $generated = $generator->generate(new FileGenerator(), $type);
        self::assertEquals($expected, $generated);
    }

    public function testBackedEnumDocblockGeneration(): void
    {
        $type = new Type(
            $this->createCodeGeneratorContext('MyNamespace'),
            'MyType',
            'MyType',
            [],
            XsdType::create('MyType')
                ->withBaseType('string')
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withIsSimple(true)
                        ->withEnums([])
                        ->withDocs('Type specific docs')
                )
        );

        $expected = <<<CODE
        <?php
        
        namespace MyNamespace;
        
        /**
         * Type specific docs
         */
        enum MyType: string {
        }
        
        CODE;


        $generator = new EnumerationGenerator();
        $generated = $generator->generate(new FileGenerator(), $type);
        self::assertEquals($expected, $generated);
    }
}
