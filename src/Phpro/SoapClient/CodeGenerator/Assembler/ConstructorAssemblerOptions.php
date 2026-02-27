<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Config\DefaultValuesStrategy;

/**
 * Class ConstructorAssemblerOptions
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ConstructorAssemblerOptions
{
    /**
     * @var bool
     */
    private $typeHints = true;

    /**
     * @var bool
     */
    private $docBlocks = true;

    /**
     * Controls how constructor parameters receive default values:
     * - None: no defaults, no reordering
     * - OptionalOnly: only nullable properties get = null; reorder
     * - All: scalars get zero-value, nullable get null; reorder
     * Requires type hints to be enabled.
     */
    private DefaultValuesStrategy $defaultValues;

    /**
     * When enabled, ALL constructor parameters are forced to be nullable with = null,
     * regardless of WSDL metadata. Takes precedence over defaultValues.
     */
    private bool $optionalValue = false;

    public function __construct()
    {
        $this->defaultValues = DefaultValuesStrategy::default();
    }

    /**
     * @return ConstructorAssemblerOptions
     */
    public static function create(): ConstructorAssemblerOptions
    {
        return new self();
    }

    /**
     * @param bool $withTypeHints
     *
     * @return ConstructorAssemblerOptions
     */
    public function withTypeHints(bool $withTypeHints = true): ConstructorAssemblerOptions
    {
        $new = clone $this;
        $new->typeHints = $withTypeHints;

        return $new;
    }

    /**
     * @return bool
     */
    public function useTypeHints(): bool
    {
        return $this->typeHints;
    }

    /**
     * @param bool $withDocBlocks
     *
     * @return ConstructorAssemblerOptions
     */
    public function withDocBlocks(bool $withDocBlocks = true): ConstructorAssemblerOptions
    {
        $new = clone $this;
        $new->docBlocks = $withDocBlocks;

        return $new;
    }

    /**
     * @return bool
     */
    public function useDocBlocks(): bool
    {
        return $this->docBlocks;
    }

    public function withDefaultValues(
        DefaultValuesStrategy $defaultValues = DefaultValuesStrategy::All
    ): ConstructorAssemblerOptions {
        $new = clone $this;
        $new->defaultValues = $defaultValues;

        return $new;
    }

    public function defaultValues(): DefaultValuesStrategy
    {
        return $this->defaultValues;
    }

    public function withOptionalValue(bool $withOptionalValue = true): ConstructorAssemblerOptions
    {
        $new = clone $this;
        $new->optionalValue = $withOptionalValue;

        return $new;
    }

    public function useOptionalValue(): bool
    {
        return $this->optionalValue;
    }
}
