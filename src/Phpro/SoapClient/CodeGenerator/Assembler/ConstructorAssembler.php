<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use Phpro\SoapClient\CodeGenerator\Config\DefaultValuesStrategy;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Provider\ScalarDefaultProvider;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\MethodGenerator;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredNullableType;

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
    public function __construct(?ConstructorAssemblerOptions $options = null)
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
        $constructor = (new MethodGenerator('__construct'))
            ->setVisibility(MethodGenerator::VISIBILITY_PUBLIC);

        $docblock = (new DocBlockGenerator())
            ->setWordWrap(false)
            ->setShortDescription('Constructor');

        $entries = $this->resolveProperties($type->getProperties());

        $body = [];
        foreach ($entries as $entry) {
            $property = $entry['property'];
            $param = new ParameterGenerator($property->getName());

            if ($this->options->useTypeHints()) {
                $param->setType($property->getPhpType());
            }

            if ($entry['hasDefault']) {
                $param->setDefaultValue($entry['default']);
            }

            $constructor->setParameter($param);
            $body[] = sprintf('$this->%1$s = $%1$s;', $property->getName());

            if ($this->options->useDocBlocks()) {
                $docblock->setTag([
                    'name' => 'param',
                    'description' => sprintf('%s $%s', $property->getDocBlockType(), $property->getName())
                ]);
            }
        }

        if ($this->options->useDocBlocks()) {
            $constructor->setDocBlock($docblock);
        }

        $constructor->setBody(implode($constructor::LINE_FEED, $body));

        return $constructor;
    }

    /**
     * Resolves properties by applying optionalValue nullability, computing default values
     * based on the configured DefaultValuesStrategy, and reordering so that properties
     * without defaults come first (PHP requirement for parameters with defaults to be trailing).
     *
     * @param list<Property> $properties
     * @return list<array{property: Property, default: mixed, hasDefault: bool}>
     */
    private function resolveProperties(array $properties): array
    {
        $strategy = $this->options->defaultValues();
        $applyDefaults = ($strategy !== DefaultValuesStrategy::None || $this->options->useOptionalValue())
            && $this->options->useTypeHints();

        $entries = [];

        foreach ($properties as $property) {
            if ($this->options->useOptionalValue()) {
                $property = $property->withMeta(fn(TypeMeta $meta): TypeMeta => $meta->withIsNullable(true));
            }

            $hasDefault = false;
            $default = null;

            if ($applyDefaults) {
                if ($strategy === DefaultValuesStrategy::All) {
                    $result = (new ScalarDefaultProvider())($property);
                    if ($result->isSucceeded()) {
                        $hasDefault = true;
                        $default = $result->getResult();
                    }
                } elseif ((new IsConsideredNullableType())($property->getMeta())) {
                    $hasDefault = true;
                    $default = null;
                }
            }

            $entries[] = ['property' => $property, 'default' => $default, 'hasDefault' => $hasDefault];
        }

        if ($applyDefaults) {
            $withoutDefaults = array_filter($entries, static fn (array $e): bool => !$e['hasDefault']);
            $withDefaults = array_filter($entries, static fn (array $e): bool => $e['hasDefault']);
            $entries = [...array_values($withoutDefaults), ...array_values($withDefaults)];
        }

        return $entries;
    }
}
