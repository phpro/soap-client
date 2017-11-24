<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Exception\AssemblerException;

/**
 * Class FinalClassAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class FinalClassAssembler implements AssemblerInterface
{
    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof TypeContext;
    }

    /**
     * @param ContextInterface|TypeContext $context
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $class->setFinal(true);
    }
}
