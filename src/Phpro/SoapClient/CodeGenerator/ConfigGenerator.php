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


    const RULESET = <<<RULESET
->addRule(
    new Rules\TypenameMatchesRule(
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\RequestAssembler()),
            new Rules\AssembleRule(new Assembler\ConstructorAssembler()),
        ]),
        '%s'
    )
)
->addRule(
    new Rules\TypenameMatchesRule(
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\ResultAssembler()),
            new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())),
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
        return sprintf("%s->%s('%s')".PHP_EOL, $file->getIndentation(), $name, $value);
    }

    /**
     * @param FileGenerator $file
     * @return string
     */
    private function getIndentedRuleSet(FileGenerator $file): string
    {
        return $file->getIndentation().preg_replace('/\n/', sprintf("\n%s", $file->getIndentation()), self::RULESET);
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
        if ($context->getRequestRegex() !== '' && $context->getResponseRegex() !== '') {
            $ruleset = $this->getIndentedRuleSet($file);
            $body .= sprintf($ruleset, $context->getRequestRegex(), $context->getResponseRegex());
        }
        $file->setBody($body.';'.PHP_EOL);

        return $file->generate();
    }
}
