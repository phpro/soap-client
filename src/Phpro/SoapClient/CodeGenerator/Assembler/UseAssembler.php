<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Exception\AssemblerException;

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
     * TraitAssembler constructor.
     * @param $useName
     * @param $useAlias
     */
    public function __construct($useName, $useAlias = null)
    {
        $this->useName = $useName;
        $this->useAlias = $useAlias;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     */
    public function canAssemble(ContextInterface $context)
    {
        return $context instanceof TypeContext;
    }

    /**
     * @param ContextInterface|TypeContext $context
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();

        try {

            if (!in_array($this->fullName(), $class->getUses())) {
                $class->addUse($this->useName, $this->useAlias);
            }

        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @return string
     */
    protected function fullName() {
        $use = $this->useName;
        if (!empty($this->useAlias)) {
            $use .= ' as ' . $this->useAlias;
        }
        return $use;
    }
}
