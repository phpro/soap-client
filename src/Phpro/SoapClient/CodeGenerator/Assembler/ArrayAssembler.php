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
 * Class ArrayAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ArrayAssembler implements AssemblerInterface
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

            $iteratorAssembler = new IteratorAssembler();
            if ($iteratorAssembler->canAssemble($context)) {
                $iteratorAssembler->assemble($context);
            }

            $interfaceAssembler = new InterfaceAssembler(\ArrayAccess::class);
            if ($interfaceAssembler->canAssemble($context)) {
                $interfaceAssembler->assemble($context);
            }

            $interfaceAssembler = new InterfaceAssembler(\Countable::class);
            if ($interfaceAssembler->canAssemble($context)) {
                $interfaceAssembler->assemble($context);
            }

            if ($firstProperty) {

                $this->implementOffsetExists($class, $firstProperty);
                $this->implementOffsetGet($class, $firstProperty);
                $this->implementOffsetSet($class, $firstProperty);
                $this->implementOffsetUnset($class, $firstProperty);
                $this->implementCount($class, $firstProperty);
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
    private function implementOffsetExists(ClassGenerator $class, Property $firstProperty)
    {
        $methodName = 'offsetExists';
        $class->removeMethod($methodName);

        $class->addMethodFromGenerator(MethodGenerator::fromArray([
            'name' => $methodName,
            'parameters' => ['name' => 'offset'],
            'body' => sprintf('return isset($this->%s[$offset]);', $firstProperty->getName()),
            'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
            'docblock' => DocBlockGeneratorFactory::fromArray([
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => 'mixed $offset An offset to check for',
                    ],
                    [
                        'name' => 'return',
                        'description' => 'boolean true on success or false on failure',
                    ],
                ],
            ]),
        ]));
    }


    /**
     * @param ClassGenerator $class
     * @param Property       $firstProperty
     *
     * @throws \Laminas\Code\Generator\Exception\InvalidArgumentException
     */
    private function implementOffsetGet(ClassGenerator $class, Property $firstProperty)
    {
        $methodName = 'offsetGet';
        $class->removeMethod($methodName);

        $class->addMethodFromGenerator(MethodGenerator::fromArray([
            'name' => $methodName,
            'parameters' => ['name' => 'offset'],
            'body' => sprintf('return $this->%s[$offset];', $firstProperty->getName()),
            'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
            'docblock' => DocBlockGeneratorFactory::fromArray([
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => 'mixed $offset An offset to check for',
                    ],
                    [
                        'name' => 'return',
                        'description' => $firstProperty->getType(),
                    ],
                ],
            ]),
        ]));
    }

    /**
     * @param ClassGenerator $class
     * @param Property       $firstProperty
     *
     * @throws \Laminas\Code\Generator\Exception\InvalidArgumentException
     */
    private function implementOffsetSet(ClassGenerator $class, Property $firstProperty)
    {
        $methodName = 'offsetSet';
        $class->removeMethod($methodName);

        $class->addMethodFromGenerator(MethodGenerator::fromArray([
            'name' => $methodName,
            'parameters' => [['name' => 'offset'], ['name' => 'value']],
            'body' => sprintf("if(!isset(\$offset)) {\n\t\$this->%s[] = \$value;\n} else {\n\t\$this->%s[\$offset] = \$value; \n}", $firstProperty->getName(), $firstProperty->getName(), $firstProperty->getName()),
            'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
            'docblock' => DocBlockGeneratorFactory::fromArray([
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => 'mixed $offset An offset to check for',
                    ]
                ],
            ]),
        ]));
    }

    /**
     * @param ClassGenerator $class
     * @param Property       $firstProperty
     *
     * @throws \Laminas\Code\Generator\Exception\InvalidArgumentException
     */
    private function implementOffsetUnset(ClassGenerator $class, Property $firstProperty)
    {
        $methodName = 'offsetUnset';
        $class->removeMethod($methodName);

        $class->addMethodFromGenerator(MethodGenerator::fromArray([
            'name' => $methodName,
            'parameters' => ['name' => 'offset'],
            'body' => sprintf('unset($this->%s[$offset]);', $firstProperty->getName()),
            'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
            'docblock' => DocBlockGeneratorFactory::fromArray([
                'tags' => [
                    [
                        'name' => 'param',
                        'description' => 'mixed $offset An offset to check for',
                    ]
                ],
            ]),
        ]));
    }

    /**
     * @param ClassGenerator $class
     * @param Property       $firstProperty
     *
     * @throws \Laminas\Code\Generator\Exception\InvalidArgumentException
     */
    private function implementCount(ClassGenerator $class, Property $firstProperty)
    {
        $methodName = 'count';
        $class->removeMethod($methodName);

        $class->addMethodFromGenerator(MethodGenerator::fromArray([
            'name' => $methodName,
            'body' => sprintf('return count($this->%s);', $firstProperty->getName()),
            'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
            'docblock' => DocBlockGeneratorFactory::fromArray([
                'tags' => [
                    [
                        'name' => 'return',
                        'description' => sprintf("%s Return count of elements", $firstProperty->getType()),
                    ],
                ],
            ]),
        ]));
    }
}
