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
    private $body = "return Config::create()\n";

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
                    new Rules\AssembleRule(new Assembler\ResponseAssembler()),
                    new Rules\AssembleRule(new Assembler\GetterAssembler()),
                ]),
                '%s'
            )
        )
RULESET;


    /**
     * @param      $name
     * @param      $value
     */
    private function addSetter($name, $value)
    {
        $this->body .= sprintf("\t->%s('%s')".PHP_EOL, $name, $value);
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
            $this->addSetter($name, $value);
        }
        $this->body .= sprintf($this->ruleset, $context->getRequestRegex(), $context->getResponseRegex());
        $file->setBody($this->body.';'.PHP_EOL);

        return $file->generate();
    }
}
