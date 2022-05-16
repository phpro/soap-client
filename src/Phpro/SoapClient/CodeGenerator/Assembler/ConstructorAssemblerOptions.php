<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

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
    private $callParentConstructor = false;

    /**
     * @var bool
     */
    private $typeHints = false;

    /**
     * @var bool
     */
    private $docBlocks = true;

    /**
     * @return ConstructorAssemblerOptions
     */
    public static function create(): ConstructorAssemblerOptions
    {
        return new self();
    }

    /**
     * @param bool $withCallParentConstructor
     *
     * @return ConstructorAssemblerOptions
     */
    public function withCallParentConstructor(bool $withCallParentConstructor = true): ConstructorAssemblerOptions
    {
        $new = clone $this;
        $new->callParentConstructor = $withCallParentConstructor;

        return $new;
    }

    /**
     * @return bool
     */
    public function useCallParentConstructor(): bool
    {
        return $this->callParentConstructor;
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
}
