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
 * Class GetterAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class GetterAssembler implements AssemblerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canAssemble(ContextInterface $context)
    {
        return $context instanceof PropertyContext;
    }

    /**
     * @param ContextInterface|PropertyContext $context
     *
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $property = $context->getProperty();
        try {
            $methodName = Normalizer::generatePropertyMethod('get', $property->getName());
            $class->removeMethod($methodName);
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray([
                    'name' => $methodName,
                    'parameters' => [],
                    'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                    'body' => sprintf('return $this->%s;', $property->getName()),
                    'docblock' => DocBlockGenerator::fromArray([
                        'tags' => [
                            [
                                'name' => 'return',
                                'description' => $property->getType()
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
