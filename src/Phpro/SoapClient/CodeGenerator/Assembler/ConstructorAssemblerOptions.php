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
    private $withTypeHints = false;

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
        $new->withTypeHints = $withTypeHints;

        return $new;
    }

    /**
     * @return bool
     */
    public function useTypeHints(): bool
    {
        return $this->withTypeHints;
    }
}
