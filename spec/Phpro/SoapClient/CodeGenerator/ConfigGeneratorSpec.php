<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\CodeGenerator\ConfigGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class ConfigGeneratorSpec
 */
class ConfigGeneratorSpec extends ObjectBehavior
{
    private $content = <<<CONTENT
<?php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\Config;

return Config::create()
	->setWsdl('wsdl.xml')
	->setTypeDestination('src/type')
	->setTypeNamespace('App\\Type')
	->setClientDestination('src/client')
	->setClientName('Client')
	->setClientNamespace('App\\Client')
	->setClassmapDestination('src/classmap')
	->setClassmapName('Classmap')
	->setClassmapNamespace('App\\Classmap')
        ->addRule(
            new Rules\TypenameMatchesRule(
                new Rules\MultiRule([
                    new Rules\AssembleRule(new Assembler\RequestAssembler()),
                    new Rules\AssembleRule(new Assembler\ConstructorAssembler()),
                ]),
                '/Request$/i'
            )
        )
        ->addRule(
            new Rules\TypenameMatchesRule(
                new Rules\MultiRule([
                    new Rules\AssembleRule(new Assembler\ResponseAssembler()),
                    new Rules\AssembleRule(new Assembler\GetterAssembler()),
                ]),
                '/Response$/i'
            )
        );

CONTENT;


    function it_is_initializable()
    {
        $this->shouldHaveType(ConfigGenerator::class);
    }

    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_generates_configs(ConfigContext $context)
    {
        $file = new FileGenerator();
        $context->getSetters()->willReturn(
            [
                'setWsdl'                => 'wsdl.xml',
                'setTypeDestination'     => 'src/type',
                'setTypeNamespace'       => 'App\\Type',
                'setClientDestination'   => 'src/client',
                'setClientName'          => 'Client',
                'setClientNamespace'     => 'App\\Client',
                'setClassmapDestination' => 'src/classmap',
                'setClassmapName'        => 'Classmap',
                'setClassmapNamespace'   => 'App\\Classmap',
            ]
        );
        $context->getRequestRegex()->willReturn('/Request$/i');
        $context->getResponseRegex()->willReturn('/Response$/i');
        $this->generate($file, $context)->shouldReturn($this->content);
    }
}
