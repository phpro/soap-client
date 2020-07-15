<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapEngineFactory;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapClientHandle;
use Phpro\SoapClient\Soap\Engine\Engine;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class ConfigGenerator
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
class ConfigGenerator implements GeneratorInterface
{
    const BODY = <<<BODY
return Config::create()

BODY;

    const RULESET_DEFAULT = <<<RULESET
->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())))
->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler()))
RULESET;


    const RULESET_REQUEST_RESPONSE = <<<RULESET
->addRule(
    new Rules\IsRequestRule(
        \$engine->getMetadata(),
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\RequestAssembler()),
            new Rules\AssembleRule(new Assembler\ConstructorAssembler(new Assembler\ConstructorAssemblerOptions())),
        ])
    )
)
->addRule(
    new Rules\IsResultRule(
        \$engine->getMetadata(),
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\ResultAssembler()),
        ])
    )
)
RULESET;

    const ENGINE_BOILERPLATE = <<<EOENGINE
->setEngine(\$engine = ExtSoapEngineFactory::fromOptions(
        ExtSoapOptions::defaults('%s', [])
            ->disableWsdlCache()
    ))
EOENGINE;



    /**
     * @param string $name
     * @param string $value
     * @param FileGenerator $file
     * @return string
     */
    private function generateSetter(string $name, string $value, FileGenerator $file): string
    {
        return sprintf("%s->%s('%s')".PHP_EOL, $file->getIndentation(), $name, $value);
    }

    /**
     * @param FileGenerator $file
     * @param string $ruleset
     * @return string
     */
    private function parseIndentedRuleSet(FileGenerator $file, string $ruleset): string
    {
        return $file->getIndentation().preg_replace('/\n/', sprintf("\n%s", $file->getIndentation()), $ruleset).PHP_EOL;
    }

    private function parseEngine(FileGenerator $fileGenerator, string $wsdl): string
    {
        return $fileGenerator->getIndentation().sprintf(self::ENGINE_BOILERPLATE, $wsdl).PHP_EOL;
    }

    /**
     * @param FileGenerator $file
     * @param ConfigContext $context
     * @return string
     */
    public function generate(FileGenerator $file, $context): string
    {
        $body = self::BODY;
        $file->setUse('Phpro\\SoapClient\\CodeGenerator\\Assembler');
        $file->setUse('Phpro\\SoapClient\\CodeGenerator\\Rules');
        $file->setUse(Config::class);
        $file->setUse(ExtSoapOptions::class);
        $file->setUse(ExtSoapEngineFactory::class);

        $body .= $this->parseEngine($file, $context->getWsdl());
        foreach ($context->getSetters() as $name => $value) {
            $body .= $this->generateSetter($name, $value, $file);
        }

        $body .= $this->parseIndentedRuleSet($file, self::RULESET_DEFAULT);
        $body .= $this->parseIndentedRuleSet($file, self::RULESET_REQUEST_RESPONSE);

        $file->setBody($body.';'.PHP_EOL);

        return $file->generate();
    }
}
