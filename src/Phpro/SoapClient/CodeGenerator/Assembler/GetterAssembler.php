<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
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
     * @var bool
     */
    private $boolGetters;

    /**
     * GetterAssembler constructor.
     * @param bool $boolGetters
     */
    public function __construct($boolGetters = false)
    {
        $this->boolGetters = $boolGetters;
    }

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
            $prefix = $this->getPrefix($property);
            $methodName = Normalizer::generatePropertyMethod($prefix, $property->getName());
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

    /**
     * @param Property $property
     * @return string
     */
    public function getPrefix(Property $property)
    {
        if (!$this->boolGetters){
            return 'get';
        }
        return $property->getType() === 'bool' ? 'is' : 'get';
    }
}
