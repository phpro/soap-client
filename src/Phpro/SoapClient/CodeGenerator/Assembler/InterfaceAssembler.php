<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Exception\AssemblerException;

/**
 * Class InterfaceAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class InterfaceAssembler implements AssemblerInterface
{
    /**
     * @var string
     */
    private $interfaceName;

    /**
     * InterfaceAssembler constructor.
     *
     * @param string $interfaceName
     */
    public function __construct(string $interfaceName)
    {
        $this->interfaceName = $interfaceName;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof TypeContext || $context instanceof PropertyContext;
    }

    /**
     * @param ContextInterface|TypeContext $context
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $interface = $this->interfaceName;

        try {
            $useAssembler = new UseAssembler($interface);
            if ($useAssembler->canAssemble($context)) {
                $useAssembler->assemble($context);
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
