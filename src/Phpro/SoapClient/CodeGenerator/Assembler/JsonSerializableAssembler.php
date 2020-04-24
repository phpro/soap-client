<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use JsonSerializable;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Class JsonSerializableAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class JsonSerializableAssembler implements AssemblerInterface
{
    /**
     * {@inheritdoc}
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
        try {
            $interfaceAssembler = new InterfaceAssembler(JsonSerializable::class);
            if ($interfaceAssembler->canAssemble($context)) {
                $interfaceAssembler->assemble($context);
            }

            $this->implementJsonSerialize($context->getType(), $context->getClass());
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @param Type $type
     * @param ClassGenerator $class
     *
     * @throws \Laminas\Code\Generator\Exception\InvalidArgumentException
     */
    private function implementJsonSerialize(Type $type, ClassGenerator $class)
    {
        $methodName = 'jsonSerialize';
        $class->removeMethod($methodName);
        $class->addMethodFromGenerator(
            MethodGenerator::fromArray([
                'name' => $methodName,
                'parameters' => [],
                'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                'body' => $this->generateJsonSerializeBody($type, $class),
                'docblock' => DocBlockGeneratorFactory::fromArray([
                    'tags' => [
                        [
                            'name' => 'return',
                            'description' => 'array'
                        ]
                    ]
                ])
            ])
        );
    }

    /**
     * @param Type $type
     * @param ClassGenerator $class
     *
     * @return string
     */
    private function generateJsonSerializeBody(Type $type, ClassGenerator $class): string
    {
        $lines = [];
        $lines[] = 'return [';

        foreach ($type->getProperties() as $property) {
            $lines[] = sprintf('%1$s\'%2$s\' => $this->%2$s,', $class->getIndentation(), $property->getName());
        }

        $lines[] = '];';

        return implode($class::LINE_FEED, $lines);
    }
}
