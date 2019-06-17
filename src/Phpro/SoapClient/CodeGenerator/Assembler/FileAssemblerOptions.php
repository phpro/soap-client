<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

/**
 * Class FileAssemblerOptions
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class FileAssemblerOptions
{
    /**
     * @var bool
     */
    private $useStrictTypes = false;

    /**
     * @return FileAssemblerOptions
     */
    public static function create(): FileAssemblerOptions
    {
        return new self();
    }

    public function withStrictTypes(): FileAssemblerOptions
    {
        $new = clone $this;
        $new->useStrictTypes = true;

        return $new;
    }

    public function useStrictTypes(): bool
    {
        return $this->useStrictTypes;
    }
}
