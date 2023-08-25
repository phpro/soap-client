<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\PropertyGenerator;
use Webmozart\Assert\Assert;

/**
 * Class PropertyAssemblerOptions
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class PropertyAssemblerOptions
{
    private bool $typeHints = true;
    private bool $docBlocks = true;
    private string $visibility = PropertyGenerator::VISIBILITY_PRIVATE;
    private bool $optionalValue = false;

    public static function create(): PropertyAssemblerOptions
    {
        return new self();
    }

    public function withTypeHints(bool $typeHints = true): self
    {
        $new = clone $this;
        $new->typeHints = $typeHints;

        return $new;
    }

    public function useTypeHints(): bool
    {
        return $this->typeHints;
    }

    public function withVisibility(string $visibility): self
    {
        Assert::inArray($visibility, [
            PropertyGenerator::VISIBILITY_PRIVATE,
            PropertyGenerator::VISIBILITY_PROTECTED,
            PropertyGenerator::VISIBILITY_PUBLIC,
        ]);

        $new = clone $this;
        $new->visibility = $visibility;

        return $new;
    }

    public function visibility(): string
    {
        return $this->visibility;
    }

    public function withDocBlocks(bool $withDocBlocks = true): self
    {
        $new = clone $this;
        $new->docBlocks = $withDocBlocks;

        return $new;
    }

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
