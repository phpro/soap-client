<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Zend\Code\Generator\FileGenerator;

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
     * TypeGenerator constructor.
     *
     * @param RuleSetInterface $ruleSet
     */
    public function __construct(RuleSetInterface $ruleSet)
    {
        $this->ruleSet = $ruleSet;
    }

    /**
     * @param FileGenerator $file
     * @param TypeMap       $typeMap
     *
     * @return string
     */
    public function generate(FileGenerator $file, $typeMap): string
    {
        $this->ruleSet->applyRules(new ClassMapContext($file, $typeMap));
        
        return $file->generate();
    }
}
