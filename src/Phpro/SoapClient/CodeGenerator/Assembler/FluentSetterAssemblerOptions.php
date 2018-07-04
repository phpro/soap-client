<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

/**
 * Class FluentSetterAssemblerOptions
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class FluentSetterAssemblerOptions
{
    /**
     * @var bool
     */
    private $typeHints = false;

    /**
     * @var bool
     */
    private $returnType = false;

    /**
     * @return FluentSetterAssemblerOptions
     */
    public static function create(): FluentSetterAssemblerOptions
    {
        return new self();
    }

    /**
     * @param bool $typeHints
     *
     * @return FluentSetterAssemblerOptions
     */
    public function withTypeHints(bool $typeHints = true): FluentSetterAssemblerOptions
    {
        $new = clone $this;
        $new->typeHints = $typeHints;

        return $new;
    }

    /**
     * @param bool $returnType
     *
     * @return FluentSetterAssemblerOptions
     */
    public function withReturnType(bool $returnType = true): FluentSetterAssemblerOptions
    {
        $new = clone $this;
        $new->returnType = $returnType;

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
    public function useReturnType(): bool
    {
        return $this->returnType;
    }
}
