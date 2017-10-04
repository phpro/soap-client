<?php

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;

/**
 * Class AssembleRule
 *
 * @package Phpro\SoapClient\CodeGenerator\Rules
 */
class AssembleRule implements RuleInterface
{
    /**
     * @var AssemblerInterface
     */
    private $assembler;

    /**
     * AssembleRule constructor.
     *
     * @param AssemblerInterface $assembler
     */
    public function __construct(AssemblerInterface $assembler)
    {
        $this->assembler = $assembler;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function appliesToContext(ContextInterface $context): bool
    {
        return $this->assembler->canAssemble($context);
    }

    /**
     * @param ContextInterface $context
     */
    public function apply(ContextInterface $context)
    {
        $this->assembler->assemble($context);
    }
}
