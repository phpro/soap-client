<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

/**
 * Class ImmutableSetterAssemblerOptions
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ImmutableSetterAssemblerOptions
{
    /**
     * @var bool
     */
    private $typeHints = true;

    /**
     * @var bool
     */
    private $returnTypes = true;

    /**
     * @var bool
     */
    private $docBlocks = true;

    /**
     * @return ImmutableSetterAssemblerOptions
     */
    public function withTypeHints(bool $typeHints = true): ImmutableSetterAssemblerOptions
    {
        $new = clone $this;
        $new->typeHints = $typeHints;

        return $new;
    }

    /**
     * @return ImmutableSetterAssemblerOptions
     */
    public function withReturnTypes(bool $returnTypes = true): ImmutableSetterAssemblerOptions
    {
        $new = clone $this;
        $new->returnTypes = $returnTypes;

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
     * @return bool
     */
    public function useReturnTypes(): bool
    {
        return $this->returnTypes;
    }

    /**
     * @return ImmutableSetterAssemblerOptions
     */
    public static function create(): ImmutableSetterAssemblerOptions
    {
        return new self();
    }

    /**
     * @param bool $withDocBlocks
     *
     * @return ImmutableSetterAssemblerOptions
     */
    public function withDocBlocks(bool $withDocBlocks = true): ImmutableSetterAssemblerOptions
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
}
