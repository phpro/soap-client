<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Class ConstructorAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ConstructorAssembler implements AssemblerInterface
{
    /**
     * @var ConstructorAssemblerOptions
     */
    private $options;

    /**
     * ConstructorAssembler constructor.
     *
     * @param ConstructorAssemblerOptions|null $options
     */
    public function __construct(ConstructorAssemblerOptions $options = null)
    {
        $this->options = $options ?? new ConstructorAssemblerOptions();
    }

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
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $type = $context->getType();

        try {
            $class->removeMethod('__construct');
            $constructor = $this->assembleConstructor($type);
            $class->addMethodFromGenerator($constructor);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @param Type $type
     *
     * @return MethodGenerator
     * @throws \Laminas\Code\Generator\Exception\InvalidArgumentException
     */
    private function assembleConstructor(Type $type): MethodGenerator
    {
        $body = [];
        $constructor = MethodGenerator::fromArray([
            'name' => '__construct',
            'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
        ]);
        $docblock = DocBlockGeneratorFactory::fromArray([
            'shortdescription' => 'Constructor'
        ]);

        foreach ($type->getProperties() as $property) {
            $body[] = sprintf('$this->%1$s = $%1$s;', $property->getName());
            $withTypeHints = $this->options->useTypeHints() ? ['type' => $property->getType()] : [];

            $constructor->setParameter(array_merge([
                'name' => $property->getName(),
            ], $withTypeHints));

            if ($this->options->useDocBlocks()) {
                $docblock->setTag([
                    'name' => 'var',
                    'description' => sprintf('%s $%s', $property->getType(), $property->getName())
                ]);
            }
        }

        if ($this->options->useDocBlocks()) {
            $constructor->setDocBlock($docblock);
        }

        $constructor->setBody(implode($constructor::LINE_FEED, $body));

        return $constructor;
    }
}
