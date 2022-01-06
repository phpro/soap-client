<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Class IteratorAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class IteratorAssembler implements AssemblerInterface
{
    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof TypeContext;
    }

    /**
     * @param ContextInterface|TypeContext $context
     *
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $properties = $context->getType()->getProperties();
        $firstProperty = count($properties) ? current($properties) : null;

        try {
            $interfaceAssembler = new InterfaceAssembler(\IteratorAggregate::class);
            if ($interfaceAssembler->canAssemble($context)) {
                $interfaceAssembler->assemble($context);
            }

            if ($firstProperty) {
                $this->implementGetIterator($class, $firstProperty);
            }
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @param ClassGenerator $class
     * @param Property       $firstProperty
     *
     * @throws \Laminas\Code\Generator\Exception\InvalidArgumentException
     */
    private function implementGetIterator(ClassGenerator $class, Property $firstProperty)
    {
        $methodName = 'getIterator';
        $class->removeMethod($methodName);
        $class->addMethodFromGenerator(
            MethodGenerator::fromArray([
                'name' => $methodName,
                'parameters' => [],
                'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                'body' => sprintf(
                    'return new \\ArrayIterator(is_array($this->%1$s) ? $this->%1$s : []);',
                    $firstProperty->getName()
                ),
                'docblock' => DocBlockGeneratorFactory::fromArray([
                    'tags' => [
                        [
                            'name' => 'return',
                            'description' => '\\ArrayIterator|'. $firstProperty->getType() .'[]'
                        ],
                        [
                            'name' => 'phpstan-return',
                            'description' => '\\ArrayIterator<array-key, '. $firstProperty->getType() .'>'
                        ],
                        [
                            'name' => 'psalm-return',
                            'description' => '\\ArrayIterator<array-key, '. $firstProperty->getType() .'>'
                        ]
                    ]
                ])
            ])
        );

        $class->setDocBlock(DocBlockGeneratorFactory::fromArray([
            'tags' => [
                [
                    'name' => 'phpstan-implements',
                    'description' => '\\IteratorAggregate<array-key, '. $firstProperty->getType() .'>'
                ],
                [
                    'name' => 'psalm-implements',
                    'description' => '\\IteratorAggregate<array-key, '. $firstProperty->getType() .'>'
                ]
            ]
        ]));
    }
}
