<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Laminas\Code\Generator\FileGenerator;

/**
 * Class ClassMapGenerator
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
class ClassMapGenerator implements GeneratorInterface
{
    /**
     * @var RuleSetInterface
     */
    private $ruleSet;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $namespace;

    /**
     * TypeGenerator constructor.
     *
     * @param RuleSetInterface $ruleSet
     * @param string           $name
     * @param string           $namespace
     */
    public function __construct(RuleSetInterface $ruleSet, string $name, string $namespace)
    {
        $this->ruleSet = $ruleSet;
        $this->name = $name;
        $this->namespace = $namespace;
    }

    /**
     * @param FileGenerator $file
     * @param TypeMap       $typeMap
     *
     * @return string
     */
    public function generate(FileGenerator $file, $typeMap): string
    {
        $this->ruleSet->applyRules(new ClassMapContext($file, $typeMap, $this->name, $this->namespace));

        return $file->generate();
    }
}
