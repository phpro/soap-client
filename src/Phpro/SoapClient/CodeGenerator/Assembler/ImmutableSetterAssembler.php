<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Class ImmutableSetterAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ImmutableSetterAssembler implements AssemblerInterface
{

    /**
     * @var ImmutableSetterAssemblerOptions
     */
    private $options;

    /**
     * ImmutableSetterAssembler constructor.
     *
     * @param ImmutableSetterAssemblerOptions|null $options
     */
    public function __construct(ImmutableSetterAssemblerOptions $options = null)
    {
        $this->options = $options ?? new ImmutableSetterAssemblerOptions();
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof PropertyContext;
    }

    /**
     * Assembles pieces of code.
     *
     * @param ContextInterface|PropertyContext $context
     *
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
            $parameterOptions = ['name' => $property->getName()];
            if ($this->options->useTypeHints()) {
                $parameterOptions['type'] = $property->getType();
            }
            $returnType = $this->options->useReturnTypes()
                ? $class->getNamespaceName() . '\\' . $class->getName()
                : null;
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray(
                    [
                        'name' => $methodName,
                        'parameters' => [$parameterOptions],
                        'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                        'body' => implode($class::LINE_FEED, $lines),
                        'returntype' => $returnType,
                        'docblock' => DocBlockGeneratorFactory::fromArray([
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
                        ]),
                    ]
                )
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
