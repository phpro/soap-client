<?php
declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\TypeEnhancer;

interface TypeEnhancer
{
    /**
     * @param non-empty-string $type
     * @return non-empty-string
     */
    public function asDocBlockType(string $type): string;

    /**
     * @param non-empty-string $type
     * @return non-empty-string
     */
    public function asPhpType(string $type): string;
}
