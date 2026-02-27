<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
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
    public function __construct(?ImmutableSetterAssemblerOptions $options = null)
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
            $methodName = $property->methodName('with');
            $class->removeMethod($methodName);
            $lines = [
                sprintf('$new = clone $this;'),
                sprintf('$new->%s = $%s;', $property->getName(), $property->parameterName()),
                '',
                sprintf('return $new;'),
            ];

            $param = (new ParameterGenerator($property->parameterName()));
            if ($this->options->useTypeHints()) {
                $param->setType($property->getPhpType());
            }

            $methodGenerator = new MethodGenerator($methodName);
            $methodGenerator->setParameter($param);
            $methodGenerator->setBody(implode($class::LINE_FEED, $lines));
            if ($this->options->useReturnTypes()) {
                $methodGenerator->setReturnType('static');
            }
            if ($this->options->useDocBlocks()) {
                $methodGenerator->setDocBlock(
                    (new DocBlockGenerator())
                        ->setWordWrap(false)
                        ->setTags([
                            [
                                'name' => 'param',
                                'description' => sprintf(
                                    '%s $%s',
                                    $property->getDocBlockType(),
                                    $property->parameterName()
                                ),
                            ],
                            [
                                'name' => 'return',
                                'description' => 'static',
                            ],
                        ])
                );
            }
            $class->addMethodFromGenerator($methodGenerator);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
