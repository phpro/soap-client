<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;

/**
 * Class TraitAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class TraitAssembler implements AssemblerInterface
{
    /**
     * @var non-empty-string
     */
    private $traitName;
    /**
     * @var non-empty-string
     */
    private $traitAlias;

    /**
     * TraitAssembler constructor.
     * @param non-empty-string $traitName
     * @param non-empty-string $traitAlias
     */
    public function __construct(string $traitName, $traitAlias = null)
    {
        $this->traitName = Normalizer::normalizeNamespace($traitName);
        $this->traitAlias = $traitAlias;
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

        try {
            $useAssembler = new UseAssembler($this->traitName, $this->traitAlias);
            if ($useAssembler->canAssemble($context)) {
                $useAssembler->assemble($context);
            }

            $traitAlias = $this->traitAlias ?: Normalizer::getClassNameFromFQN($this->traitName);

            $class->addTrait($traitAlias);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
