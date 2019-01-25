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
    private $typeHints = false;

    /**
     * @return ImmutableSetterAssemblerOptions
     */
    public function withTypeHints(): ImmutableSetterAssemblerOptions
    {
        $new = clone $this;
        $new->typeHints = true;

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
     * @return ImmutableSetterAssemblerOptions
     */
    public static function create(): ImmutableSetterAssemblerOptions
    {
        return new self();
    }
}
