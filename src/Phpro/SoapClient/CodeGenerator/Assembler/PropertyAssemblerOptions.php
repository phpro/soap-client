<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\PropertyGenerator;
use Phpro\SoapClient\CodeGenerator\Config\DefaultValuesStrategy;
use Webmozart\Assert\Assert;

/**
 * Class PropertyAssemblerOptions
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class PropertyAssemblerOptions
{
    private bool $typeHints = true;
    private bool $docBlocks = true;
    private string $visibility = PropertyGenerator::VISIBILITY_PRIVATE;

    /**
     * When enabled, ALL properties are forced to be nullable with = null, regardless of WSDL metadata.
     * This makes every property ?Type = null, which is useful when you want to construct
     * objects without providing all values upfront.
     *
     * Takes precedence over `defaultValues`: when both are enabled, all properties become ?Type = null.
     */
    private bool $optionalValue = false;

    /**
     * Controls how properties receive default values:
     * - None: no defaults from this option
     * - OptionalOnly: only nullable properties get = null (already handled by the nullable check)
     * - All: scalars get zero-value, nullable get null, non-nullable complex types get no default
     *
     * This differs from `optionalValue`: optionalValue forces ALL properties to ?Type = null
     * regardless of WSDL metadata. When both are enabled, optionalValue takes precedence.
     */
    private DefaultValuesStrategy $defaultValues;

    public function __construct()
    {
        $this->defaultValues = DefaultValuesStrategy::default();
    }

    public static function create(): PropertyAssemblerOptions
    {
        return new self();
    }

    public function withTypeHints(bool $typeHints = true): self
    {
        $new = clone $this;
        $new->typeHints = $typeHints;

        return $new;
    }

    public function useTypeHints(): bool
    {
        return $this->typeHints;
    }

    public function withVisibility(string $visibility): self
    {
        Assert::inArray($visibility, [
            PropertyGenerator::VISIBILITY_PRIVATE,
            PropertyGenerator::VISIBILITY_PROTECTED,
            PropertyGenerator::VISIBILITY_PUBLIC,
        ]);

        $new = clone $this;
        $new->visibility = $visibility;

        return $new;
    }

    public function visibility(): string
    {
        return $this->visibility;
    }

    public function withDocBlocks(bool $withDocBlocks = true): self
    {
        $new = clone $this;
        $new->docBlocks = $withDocBlocks;

        return $new;
    }

    public function useDocBlocks(): bool
    {
        return $this->docBlocks;
    }

    public function withOptionalValue(bool $withOptionalValue = true): self
    {
        $new = clone $this;
        $new->optionalValue = $withOptionalValue;

        return $new;
    }

    public function useOptionalValue(): bool
    {
        return $this->optionalValue;
    }

    public function withDefaultValues(DefaultValuesStrategy $defaultValues = DefaultValuesStrategy::All): self
    {
        $new = clone $this;
        $new->defaultValues = $defaultValues;

        return $new;
    }

    public function defaultValues(): DefaultValuesStrategy
    {
        return $this->defaultValues;
    }
}
