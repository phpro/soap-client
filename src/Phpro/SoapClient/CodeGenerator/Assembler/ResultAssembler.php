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
        $class = $context->getClass();
        $interface = ResultInterface::class;

        try {
            if (!in_array($interface, $class->getUses())) {
                $class->addUse($interface);
            }

            $interfaces = $class->getImplementedInterfaces();
            if (!in_array($interface, $interfaces)) {
                $interfaces[] = $interface;
                $class->setImplementedInterfaces($interfaces);
            }

        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
