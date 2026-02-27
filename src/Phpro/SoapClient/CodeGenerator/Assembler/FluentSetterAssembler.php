<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Class SetterAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class FluentSetterAssembler implements AssemblerInterface
{
    /**
     * @var FluentSetterAssemblerOptions
     */
    private $options;

    /**
     * FluentSetterAssembler constructor.
     *
     * @param FluentSetterAssemblerOptions|null $options
     */
    public function __construct(?FluentSetterAssemblerOptions $options = null)
    {
        $this->options = $options ?? new FluentSetterAssemblerOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof PropertyContext;
    }

    /**
     * @param ContextInterface|PropertyContext $context
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $property = $context->getProperty();
        try {
            $methodName = $property->methodName('set');
            $class->removeMethod($methodName);

            $methodGenerator = new MethodGenerator($methodName);
            $methodGenerator->setParameter($this->getParameter($property));
            $methodGenerator->setVisibility(MethodGenerator::VISIBILITY_PUBLIC);
            $methodGenerator->setBody(sprintf(
                '$this->%s = $%s;%sreturn $this;',
                $property->getName(),
                $property->parameterName(),
                $class::LINE_FEED
            ));
            if ($this->options->useReturnType()) {
                $methodGenerator->setReturnType('static');
            }
            if ($this->options->useDocBlocks()) {
                $methodGenerator->setDocBlock(
                    (new DocBlockGenerator())
                        ->setWordWrap(false)
                        ->setTags([
                            [
                                'name'        => 'param',
                                'description' => sprintf(
                                    '%s $%s',
                                    $property->getDocBlockType(),
                                    $property->parameterName()
                                ),
                            ],
                            [
                                'name'        => 'return',
                                'description' => '$this',
                            ]
                        ])
                );
            }
            $class->addMethodFromGenerator($methodGenerator);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    private function getParameter(Property $property): ParameterGenerator
    {
        $param = (new ParameterGenerator($property->parameterName()));
        if ($this->options->useTypeHints()) {
            $param->setType($property->getPhpType());
        }

        return $param;
    }
}
