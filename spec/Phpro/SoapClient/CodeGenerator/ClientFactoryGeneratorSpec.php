<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\ClientFactoryGenerator;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class ClientFactoryGeneratorSpec
 */
class ClientFactoryGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ClientFactoryGenerator::class);
    }

    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }
}
