<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Exception\AssemblerException;

/**
 * Class IteratorAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class IteratorAssembler implements AssemblerInterface
{
    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context)
    {
        return $context instanceof TypeContext;
    }

    /**
     * @param ContextInterface|TypeContext $context
     *
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        throw new AssemblerException('Not implemented yet!');

        // TODO: this assembler will add an \IteratorAggregate interface and a getIterator method.
        // TODO: It is also a possibility to define an ArrayAccess interface
    }
}
