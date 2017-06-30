<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;

/**
 * Class ImmutableSetterAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ImmutableSetterAssembler implements AssemblerInterface
{

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context)
    {
        return $context instanceof PropertyContext;
    }

    /**
     * Assembles pieces of code.
     *
     * @param ContextInterface|PropertyContext $context
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $property = $context->getProperty();
        try {
            $methodName = Normalizer::generatePropertyMethod('with', $property->getName());
            $class->removeMethod($methodName);
            $lines = [
                sprintf('$new = clone $this;'),
                sprintf('$new->%1$s = $%1$s;', $property->getName()),
                '',
                sprintf('return $new;'),
            ];
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray(
                    [
                        'name' => $methodName,
                        'parameters' => [$property->getName()],
                        'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                        'body' => implode($class::LINE_FEED, $lines),
                        'docblock' => DocBlockGenerator::fromArray(
                            [
                                'tags' => [
                                    [
                                        'name' => 'param',
                                        'description' => sprintf('%s $%s', $property->getType(), $property->getName()),
                                    ],
                                    [
                                        'name' => 'return',
                                        'description' => $class->getName(),
                                    ],
                                ],
                            ]
                        ),
                    ]
                )
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
