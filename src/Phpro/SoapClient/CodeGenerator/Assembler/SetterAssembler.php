<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
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
    public function __construct(SetterAssemblerOptions $options = null)
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
            $parameterOptions = ['name' => $property->getName()];
            if ($this->options->useTypeHints()) {
                $parameterOptions['type'] = $property->getType();
            }
            $methodName = Normalizer::generatePropertyMethod('set', $property->getName());
            $class->removeMethod($methodName);
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray(
                    [
                        'name' => $methodName,
                        'parameters' => [$parameterOptions],
                        'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                        'body' => sprintf('$this->%1$s = $%1$s;', $property->getName()),
                        'docblock' => DocBlockGeneratorFactory::fromArray([
                            'tags' => [
                                [
                                    'name' => 'param',
                                    'description' => sprintf('%s $%s', $property->getType(), $property->getName()),
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
