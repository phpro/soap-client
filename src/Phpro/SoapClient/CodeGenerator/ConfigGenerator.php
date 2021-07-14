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

    const RULESET_RESPONSE = <<<RULESET
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
        return sprintf("%s->%s('%s')".GeneratorInterface::EOL, $file->getIndentation(), $name, $value);
    }

    /**
     * @param FileGenerator $file
     * @param string $ruleset
     * @return string
     */
    private function parseIndentedRuleSet(FileGenerator $file, string $ruleset): string
    {
        return $file->getIndentation().preg_replace('/\n/', sprintf("\n%s", $file->getIndentation()), $ruleset)
            .GeneratorInterface::EOL;
    }

    private function parseEngine(FileGenerator $fileGenerator, string $wsdl): string
    {
        return $fileGenerator->getIndentation().sprintf(self::ENGINE_BOILERPLATE, $wsdl).GeneratorInterface::EOL;
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

        $body .= $this->parseIndentedRuleSet($file, $this->generateGetterSetterRuleSet($context));
        $body .= $this->parseIndentedRuleSet($file, $this->generateRequestRuleSet($context));
        $body .= $this->parseIndentedRuleSet($file, self::RULESET_RESPONSE);

        $file->setBody($body.';'.GeneratorInterface::EOL);

        return $file->generate();
    }

    private function generateGetterSetterRuleSet(ConfigContext $context): string
    {
        if ($context->isGenerateDocblocks()) {
            return <<<RULESET
->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())))
->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler(
    new Assembler\ImmutableSetterAssemblerOptions()
)))
RULESET;
        }

        return <<<RULESET
->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(
    (new Assembler\GetterAssemblerOptions())->withDocBlocks(false)
)))
->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler(
    (new Assembler\ImmutableSetterAssemblerOptions())->withDocBlocks(false)
)))
RULESET;
    }

    private function generateRequestRuleSet(ConfigContext $context): string
    {
        if ($context->isGenerateDocblocks()) {
            return <<<REQUEST
->addRule(
    new Rules\IsRequestRule(
        \$engine->getMetadata(),
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\RequestAssembler()),
            new Rules\AssembleRule(new Assembler\ConstructorAssembler(new Assembler\ConstructorAssemblerOptions())),
        ])
    )
)
REQUEST;
        }

        return <<<REQUEST
->addRule(
    new Rules\IsRequestRule(
        \$engine->getMetadata(),
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\RequestAssembler()),
            new Rules\AssembleRule(new Assembler\ConstructorAssembler(
                (new Assembler\ConstructorAssemblerOptions())->withDocBlocks(false)
            )),
        ])
    )
)
REQUEST;
    }
}
