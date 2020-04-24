<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\CodeGenerator\Util\TypeChecker;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
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
    public function __construct(FluentSetterAssemblerOptions $options = null)
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
            $methodName = Normalizer::generatePropertyMethod('set', $property->getName());
            $class->removeMethod($methodName);
            $class->addMethodFromGenerator(
                MethodGenerator::fromArray([
                    'name'       => $methodName,
                    'parameters' => $this->getParameter($property),
                    'visibility' => MethodGenerator::VISIBILITY_PUBLIC,
                    'body'       => sprintf(
                        '$this->%1$s = $%1$s;%2$sreturn $this;',
                        $property->getName(),
                        $class::LINE_FEED
                    ),
                    'returntype' => $this->options->useReturnType()
                        ? $class->getNamespaceName().'\\'.$class->getName()
                        : null,
                    'docblock'   => DocBlockGeneratorFactory::fromArray([
                        'tags' => [
                            [
                                'name'        => 'param',
                                'description' => sprintf('%s $%s', $property->getType(), $property->getName()),
                            ],
                            [
                                'name'        => 'return',
                                'description' => '$this',
                            ],
                        ],
                    ]),
                ])
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @param Property $property
     *
     * @return array
     */
    private function getParameter(Property $property): array
    {
        $type = $property->getType();
        if (TypeChecker::isKnownType($type) && $this->options->useTypeHints()) {
            return [
                [
                    'name' => $property->getName(),
                    'type' => $type,
                ],
            ];
        }

        return [$property->getName()];
    }
}
