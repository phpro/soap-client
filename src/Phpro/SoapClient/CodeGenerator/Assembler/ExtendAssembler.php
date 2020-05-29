<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\ClassGenerator;

/**
 * Class ExtendAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ExtendAssembler implements AssemblerInterface
{
    /**
     * @var string
     */
    private $extendedClassName;

    /**
     * ExtendAssembler constructor.
     *
     * @param string $extendedClassName
     */
    public function __construct(string $extendedClassName)
    {
        $this->extendedClassName = $extendedClassName;
    }

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
        $extendedClassName = $this->extendedClassName;

        if ($this->isExtendingItself($class)) {
            return;
        }

        try {
            $useAssembler = new UseAssembler($extendedClassName);
            if ($useAssembler->canAssemble($context)) {
                $useAssembler->assemble($context);
            }

            $class->setExtendedClass($extendedClassName);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @param ClassGenerator $class
     * @return bool
     */
    private function isExtendingItself(ClassGenerator $class): bool
    {
        $fullClassName = $class->getNamespaceName() . '\\' . $class->getName();

        return $this->extendedClassName === $fullClassName;
    }
}
