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
    private $docBlocks = true;

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
     * @return bool
     */
    public function useTypeHints(): bool
    {
        return $this->typeHints;
    }

    /**
     * @param bool $withDocBlocks
     *
     * @return SetterAssemblerOptions
     */
    public function withDocBlocks(bool $withDocBlocks = true): SetterAssemblerOptions
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
