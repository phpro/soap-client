<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\TypeGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\PropertyGenerator;

/**
 * Class PropertyAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class PropertyAssembler implements AssemblerInterface
{
    private PropertyAssemblerOptions $options;

    public function __construct(?PropertyAssemblerOptions $options = null)
    {
        $this->options = $options ?? PropertyAssemblerOptions::create();
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
            // It's not possible to overwrite a property in laminas-code yet!
            if ($class->hasProperty($property->getName())) {
                return;
            }

            $propertyGenerator = PropertyGenerator::fromArray([
                'name' => $property->getName(),
                'visibility' => $this->options->visibility(),
                'omitdefaultvalue' => true,
            ]);

            if ($this->options->useDocBlocks()) {
                $propertyGenerator->setDocBlock(
                    DocBlockGeneratorFactory::fromArray([
                        'longdescription' => $property->getMeta()->docs()->unwrapOr(''),
                        'tags' => [
                            [
                                'name'        => 'var',
                                'description' => $property->getDocBlockType(),
                            ],
                        ]
                    ])
                );
            }

            if ($this->options->useTypeHints()) {
                $propertyGenerator->setType(TypeGenerator::fromTypeString($property->getPhpType()));
            }

            $class->addPropertyFromGenerator($propertyGenerator);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
