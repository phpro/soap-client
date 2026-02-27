<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Phpro\SoapClient\CodeGenerator\Config\DefaultValuesStrategy;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
use Phpro\SoapClient\CodeGenerator\Provider\ScalarDefaultProvider;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\PropertyGenerator;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredNullableType;

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

        // Always make sure properties are nullable!
        if ($this->options->useOptionalValue()) {
            $property = $property->withMeta(fn(TypeMeta $meta): TypeMeta => $meta->withIsNullable(true));
        }

        try {
            // This makes it easier to overwrite the default property assembler with your own:
            // It will remove the existing one and recreate the property
            if ($class->hasProperty($property->getName())) {
                $class->removeProperty($property->getName());
            }

            $propertyGenerator = (new PropertyGenerator($property->getName()))
                ->setVisibility($this->options->visibility())
                ->omitDefaultValue(
                    !$this->options->useOptionalValue() && !(new IsConsideredNullableType())($property->getMeta())
                );

            if ($this->options->useDocBlocks()) {
                $propertyGenerator->setDocBlock(
                    (new DocBlockGenerator())
                        ->setWordWrap(false)
                        ->setLongDescription($property->getMeta()->docs()->unwrapOr(''))
                        ->setTags([
                            [
                                'name'        => 'var',
                                'description' => $property->getDocBlockType(),
                            ],
                        ])
                );
            }

            if ($this->options->useTypeHints()) {
                $propertyGenerator->setType(TypeGenerator::fromTypeString($property->getPhpType()));
            }

            if ($this->options->defaultValues() === DefaultValuesStrategy::All) {
                $defaultValue = (new ScalarDefaultProvider())($property);
                if ($defaultValue->isSucceeded()) {
                    $propertyGenerator
                        ->setDefaultValue(
                            $defaultValue->getResult(),
                            PropertyValueGenerator::TYPE_AUTO,
                            PropertyValueGenerator::OUTPUT_SINGLE_LINE
                        )
                        ->omitDefaultValue(false);
                }
            }

            $class->addPropertyFromGenerator($propertyGenerator);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
