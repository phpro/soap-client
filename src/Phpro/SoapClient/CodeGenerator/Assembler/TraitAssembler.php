<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Exception\AssemblerException;

/**
 * Class TraitAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class TraitAssembler implements AssemblerInterface
{
    /**
     * @var string
     */
    private $traitName;
    /**
     * @var string
     */
    private $traitAlias;

    /**
     * TraitAssembler constructor.
     * @param $traitName
     * @param $traitAlias
     */
    public function __construct ($traitName, $traitAlias = null) {
        $this->traitName = $traitName;
        $this->traitAlias = $traitAlias;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     */
    public function canAssemble (ContextInterface $context) {
        return $context instanceof TypeContext;
    }

    /**
     * @param ContextInterface|TypeContext $context
     */
    public function assemble (ContextInterface $context) {
        $class = $context->getClass();

        try {
            if (!in_array($this->traitName, $class->getUses())) {
                $class->addUse($this->traitName, $this->traitAlias);
            }
            $traitAlias = $this->traitAlias;
            if (!$traitAlias) {
                $a = explode('\\', $this->traitName);
                $traitAlias = array_pop($a);
            }
            $traits = $class->getTraits();
            if (!in_array($traitAlias, $traits)) {
                $class->addTrait($traitAlias);
            }
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
