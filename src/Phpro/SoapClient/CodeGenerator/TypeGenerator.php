<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;

/**
 * Class TypeGenerator
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
class TypeGenerator implements GeneratorInterface
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
     * @param Type          $type
     *
     * @return string
     */
    public function generate(FileGenerator $file, $type): string
    {
        $class = $file->getClass() ?: new ClassGenerator();
        $class->setNamespaceName($type->getNamespace());
        $class->setName($type->getName());

        if ($duplicateType = $type->getDuplicateType()) {
            if (!$class->hasConstant('XSD_NAMESPACE')) {
                $class->addConstant('XSD_NAMESPACE', $duplicateType->getXsdNamespace());
            }

            if (!$class->hasMethod('fromXml')) {
                $class->addMethodFromGenerator(
                    MethodGenerator::fromArray(
                        [
                            'name'       => 'fromXml',
                            'parameters' => [
                                [
                                    'name' => 'xml',
                                    'type' => 'string',
                                ],
                            ],
                            'static'     => true,
                            'body'       => <<<BODY
\$simpleXml = simplexml_load_string(\$xml);
\$root = \$simpleXml->children(self::XSD_NAMESPACE);
\$root->registerXPathNamespace('default', self::XSD_NAMESPACE);

return \$root;
BODY
                        ]
                    )
                );
            }
        }

        $this->ruleSet->applyRules(new TypeContext($class, $type));

        foreach ($type->getProperties() as $property) {
            $this->ruleSet->applyRules(new PropertyContext($class, $type, $property));
        }

        $file->setClass($class);

        return $file->generate();
    }
}
