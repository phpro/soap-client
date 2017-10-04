<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;

/**
 * Class SetterAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class FluentSetterAssembler implements AssemblerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof PropertyContext;
    }

    /**
     * @param ContextInterface|PropertyContext $context
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $property = $context->getProperty();
        try {
            $methodName = Normalizer::generatePropertyMethod('set', $property->getName());
            $class->removeMethod($methodName);
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray([
                    'name' => $methodName,
                    'parameters' => [$property->getName()],
                    'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                    'body' => sprintf('$this->%1$s = $%1$s;%2$sreturn $this;', $property->getName(), $class::LINE_FEED),
                    'docblock' => DocBlockGenerator::fromArray([
                        'tags' => [
                            [
                                'name' => 'param',
                                'description' => sprintf('%s $%s', $property->getType(), $property->getName())
                            ],
                            [
                                'name' => 'return',
                                'description' => '$this'
                            ]
                        ]
                    ])
                ])
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
