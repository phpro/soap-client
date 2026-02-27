<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Class SetterAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class SetterAssembler implements AssemblerInterface
{
    /**
     * @var SetterAssemblerOptions
     */
    private $options;

    /**
     * SetterAssembler constructor.
     *
     * @param SetterAssemblerOptions|null $options
     */
    public function __construct(?SetterAssemblerOptions $options = null)
    {
        $this->options = $options ?? new SetterAssemblerOptions();
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
     *
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $property = $context->getProperty();
        try {
            $methodName = $property->methodName('set');
            $class->removeMethod($methodName);

            $param = (new ParameterGenerator($property->parameterName()));
            if ($this->options->useTypeHints()) {
                $param->setType($property->getPhpType());
            }

            $methodGenerator = new MethodGenerator($methodName);
            $methodGenerator->setReturnType('void');
            $methodGenerator->setParameter($param);
            $methodGenerator->setVisibility(MethodGenerator::VISIBILITY_PUBLIC);
            $methodGenerator->setBody(sprintf('$this->%s = $%s;', $property->getName(), $property->parameterName()));
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
                            ]
                        ])
                );
            }

            $class->addMethodFromGenerator($methodGenerator);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
