<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;

/**
 * Class ResultAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ResultAssembler implements AssemblerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canAssemble(ContextInterface $context)
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
        $interface = ResultProviderInterface::class;
        $properties = $context->getType()->getProperties();
        $firstProperty = count($properties) ? current($properties) : null;

        try {
            if (!in_array($interface, $class->getUses())) {
                $class->addUse($interface);
            }

            $interfaces = $class->getImplementedInterfaces();
            if (!in_array($interface, $interfaces)) {
                $interfaces[] = $interface;
                $class->setImplementedInterfaces($interfaces);
            }

            if ($firstProperty) {
                $this->implementMethod($class, $firstProperty);
            }


        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @param ClassGenerator $class
     * @param Property       $property
     *
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     */
    private function implementMethod(ClassGenerator $class, Property $property)
    {
        $methodName = 'getResult';
        $class->removeMethod($methodName);
        $class->addMethodFromGenerator(
            MethodGenerator::fromArray([
                'name' => $methodName,
                'parameters' => [],
                'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                'body' => sprintf('return $this->%s();', Normalizer::generatePropertyMethod('get', $property->getName())),
                'docblock' => DocBlockGenerator::fromArray([
                    'tags' => [
                        [
                            'name' => 'return',
                            'description' => $property->getType() . '|' . ResultInterface::class
                        ]
                    ]
                ])
            ])
        );
    }
}
