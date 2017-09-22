<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Model\Type;

/**
 * Class UseAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class TypeModelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    function it_should_convert_underscores_in_path()
    {
        $assembler = new Type('\Foo\Bar', 'Bar_3_2', []);
        $this->assertEquals(
            'src'.DIRECTORY_SEPARATOR.'Bar'.DIRECTORY_SEPARATOR.'3'.DIRECTORY_SEPARATOR.'2.php',
            $assembler->getPathname('src')
        );
    }
}
