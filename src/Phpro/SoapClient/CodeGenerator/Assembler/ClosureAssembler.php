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
class ClosureAssembler implements AssemblerInterface
{

    /**
     * @var callable
     */
    private $canAssembleClosure;

    /**
     * @var callable
     */
    private $assembleClosure;


    /**
     * ClosureAssembler constructor.
     * @param callable $canAssembleClosure
     * @param callable $assembleClosure
     */
    public function __construct(callable $canAssembleClosure, callable $assembleClosure)
    {
        $this->canAssembleClosure = $canAssembleClosure;
        $this->assembleClosure = $assembleClosure;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return call_user_func($this->canAssembleClosure, $context);
    }

    /**
     * @param ContextInterface|TypeContext $context
     */
    public function assemble(ContextInterface $context)
    {
        call_user_func($this->assembleClosure, $context);
    }
}
