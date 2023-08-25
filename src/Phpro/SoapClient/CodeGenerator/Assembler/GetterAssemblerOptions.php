<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

/**
 * Class GetterAssemblerOptions
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class GetterAssemblerOptions
{
    /**
     * @var bool
     */
    private $boolGetters = false;

    /**
     * @var bool
     */
    private $returnType = true;

    /**
     * @var bool
     */
    private $docBlocks = true;
    private bool $optionalValue = false;

    /**
     * @return GetterAssemblerOptions
     */
    public static function create(): GetterAssemblerOptions
    {
        return new self();
    }

    /**
     * @param bool $boolGetters
     *
     * @return GetterAssemblerOptions
     */
    public function withBoolGetters(bool $boolGetters = true): GetterAssemblerOptions
    {
        $new = clone $this;
        $new->boolGetters = $boolGetters;

        return $new;
    }

    /**
     * @param bool $returnType
     *
     * @return GetterAssemblerOptions
     */
    public function withReturnType(bool $returnType = true): GetterAssemblerOptions
    {
        $new = clone $this;
        $new->returnType = $returnType;

        return $new;
    }

    /**
     * @return bool
     */
    public function useBoolGetters(): bool
    {
        return $this->boolGetters;
    }

    /**
     * @return bool
     */
    public function useReturnType(): bool
    {
        return $this->returnType;
    }

    /**
     * @param bool $withDocBlocks
     *
     * @return GetterAssemblerOptions
     */
    public function withDocBlocks(bool $withDocBlocks = true): GetterAssemblerOptions
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
}
