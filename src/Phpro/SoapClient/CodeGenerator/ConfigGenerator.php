<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use Zend\Code\Generator\FileGenerator;

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
    new Rules\TypenameMatchesRule(
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\RequestAssembler()),
            new Rules\AssembleRule(new Assembler\ConstructorAssembler(new Assembler\ConstructorAssemblerOptions())),
        ]),
        '%s'
    )
)
->addRule(
    new Rules\TypenameMatchesRule(
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\ResultAssembler()),
        ]),
        '%s'
    )
)
RULESET;


    /**
     * @param string $name
     * @param string $value
     * @param FileGenerator $file
     * @return string
     */
    private function generateSetter(string $name, string $value, FileGenerator $file): string
    {
        return sprintf("%s->%s('%s')".FileGenerator::LINE_FEED, $file->getIndentation(), $name, $value);
    }

    /**
     * @param FileGenerator $file
     * @param string $ruleset
     * @return string
     */
    private function parseIndentedRuleSet(FileGenerator $file, string $ruleset): string
    {
        return $file->getIndentation()
            .preg_replace('/\n/', sprintf("\n%s", $file->getIndentation()), $ruleset)
            .FileGenerator::LINE_FEED;
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

        foreach ($context->getSetters() as $name => $value) {
            $body .= $this->generateSetter($name, $value, $file);
        }

        $body .= $this->parseIndentedRuleSet($file, self::RULESET_DEFAULT);

        if ($context->getRequestRegex() !== '' && $context->getResponseRegex() !== '') {
            $rules = $this->parseIndentedRuleSet($file, self::RULESET_REQUEST_RESPONSE);
            $body .= sprintf($rules, $context->getRequestRegex(), $context->getResponseRegex());
        }

        $file->setBody($body.';'.FileGenerator::LINE_FEED);

        return $file->generate();
    }
}
