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
    private $returnType = false;

    /**
     * @return FluentSetterAssemblerOptions
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param bool $returnType
     *
     * @return FluentSetterAssemblerOptions
     */
    public function withReturnType(bool $returnType): FluentSetterAssemblerOptions
    {
        $new = clone $this;
        $new->returnType = $returnType;

        return $new;
    }

    /**
     * @return bool
     */
    public function useReturnType(): bool
    {
        return $this->returnType;
    }
}
