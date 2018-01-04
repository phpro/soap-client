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
    private $body = <<<BODY
return Config::create()

BODY;


    private $ruleset = <<<RULESET
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
     * @param               $name
     * @param               $value
     * @param FileGenerator $file
     */
    private function addSetter($name, $value, FileGenerator $file)
    {
        $this->body .= sprintf("%s->%s('%s')".PHP_EOL, $file->getIndentation(), $name, $value);
    }

    /**
     * @param FileGenerator $file
     * @return string
     */
    private function getIndentedRuleSet(FileGenerator $file): string
    {
        return $file->getIndentation().preg_replace('/\n/', sprintf("\n%s", $file->getIndentation()), $this->ruleset);
    }

    /**
     * @param FileGenerator $file
     * @param ConfigContext $context
     * @return string
     */
    public function generate(FileGenerator $file, $context): string
    {
        $file->setUse('Phpro\\SoapClient\\CodeGenerator\\Assembler');
        $file->setUse('Phpro\\SoapClient\\CodeGenerator\\Rules');
        $file->setUse(Config::class);

        foreach ($context->getSetters() as $name => $value) {
            $this->addSetter($name, $value, $file);
        }
        $ruleset = $this->getIndentedRuleSet($file);
        $this->body .= sprintf($ruleset, $context->getRequestRegex(), $context->getResponseRegex());
        $file->setBody($this->body.';'.PHP_EOL);

        return $file->generate();
    }
}
