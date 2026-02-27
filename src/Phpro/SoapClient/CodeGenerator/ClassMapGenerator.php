<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\FileContext;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Laminas\Code\Generator\FileGenerator;

/**
 * @template-implements GeneratorInterface<TypeMap>
 */
class ClassMapGenerator implements GeneratorInterface
{
    public function __construct(
        private RuleSetInterface $ruleSet,
        private ClassMapConfig $classMap
    ) {
    }

    /**
     * @param FileGenerator $file
     * @param TypeMap       $typeMap
     *
     * @return string
     */
    public function generate(FileGenerator $file, $typeMap): string
    {
        $this->ruleSet->applyRules(
            new ClassMapContext($file, $typeMap, $this->classMap, $typeMap->getCodeGeneratorContext())
        );
        $this->ruleSet->applyRules(new FileContext($file));

        return $file->generate();
    }
}
