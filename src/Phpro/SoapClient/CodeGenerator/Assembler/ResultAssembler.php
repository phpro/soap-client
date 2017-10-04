<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Exception\AssemblerException;
use Phpro\SoapClient\Type\ResultInterface;

/**
 * Class ResultAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ResultAssembler implements AssemblerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canAssemble(ContextInterface $context): bool
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
        try {
            $interfaceAssembler = new InterfaceAssembler(ResultInterface::class);
            if ($interfaceAssembler->canAssemble($context)) {
                $interfaceAssembler->assemble($context);
            }
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
