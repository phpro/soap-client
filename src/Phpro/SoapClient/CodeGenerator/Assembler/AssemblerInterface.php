<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\Exception\AssemblerException;

/**
 * Interface AssemblerInterface
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
interface AssemblerInterface
{

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context): bool;

    /**
     * Assembles pieces of code.
     *
     * @param ContextInterface $context
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context);
}
