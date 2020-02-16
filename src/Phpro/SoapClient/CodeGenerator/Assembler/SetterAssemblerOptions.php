<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

/**
 * Class SetterAssemblerOptions
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class SetterAssemblerOptions
{
    /**
     * @var bool
     */
    private $typeHints = false;

    /**
     * @var bool
     */
    private $normalizeValue = false;

    /**
     * @return SetterAssemblerOptions
     */
    public static function create(): SetterAssemblerOptions
    {
        return new self();
    }

    /**
     * @param bool $typeHints
     *
     * @return SetterAssemblerOptions
     */
    public function withTypeHints(bool $typeHints = true): SetterAssemblerOptions
    {
        $new = clone $this;
        $new->typeHints = $typeHints;
        return $new;
    }

    /**
     * @param bool $normalizeValue
     *
     * @return SetterAssemblerOptions
     */
    public function withNormalizeValue(bool $normalizeValue = true): SetterAssemblerOptions
    {
        $new = clone $this;
        $new->normalizeValue = $normalizeValue;

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
    public function useNormalizeValue(): bool
    {
        return $this->normalizeValue;
    }
}
