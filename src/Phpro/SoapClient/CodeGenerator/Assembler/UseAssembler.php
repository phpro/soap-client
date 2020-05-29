<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\ClassGenerator;

/**
 * Class UseAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class UseAssembler implements AssemblerInterface
{
    /**
     * @var string
     */
    private $useName;

    /**
     * @var string
     */
    private $useAlias;

    /**
     * UseAssembler constructor.
     * @param string $useName
     * @param string $useAlias
     */
    public function __construct(string $useName, string $useAlias = null)
    {
        $this->useName = $useName;
        $this->useAlias = $useAlias;
    }

    /**
     * @param ContextInterface $context
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

        if ($this->usesTheSameNamespace($class)) {
            return;
        }

        try {
            $uses = $class->getUses();

            if (!in_array(Normalizer::getCompleteUseStatement($this->useName, $this->useAlias), $uses)
                && !in_array($this->useName, $uses)
            ) {
                $class->addUse($this->useName, $this->useAlias);
            }
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @param ClassGenerator $class
     * @return bool
     */
    private function usesTheSameNamespace(ClassGenerator $class): bool
    {
        $namespaceName = (string) $class->getNamespaceName();

        if ($this->usesGlobalNamespace($namespaceName)) {
            return true;
        }

        return in_array($namespaceName, [$this->useName, $this->getClassUseNamespaceName()]);
    }

    /**
     * @param string $namespaceName
     *
     * @return bool
     */
    private function usesGlobalNamespace(string $namespaceName): bool
    {
        return '' === $namespaceName && false === strpos($this->useName, '\\');
    }

    /**
     * @return string
     */
    private function getClassUseNamespaceName(): string
    {
        return substr($this->useName, 0, strrpos($this->useName, '\\'));
    }
}
