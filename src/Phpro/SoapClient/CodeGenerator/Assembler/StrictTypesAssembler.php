<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\DeclareStatement;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\FileContext;

/**
 * Class StrictTypesAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class StrictTypesAssembler implements AssemblerInterface
{
    /**
     * @inheritDoc
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof FileContext;
    }

    /**
     * @param ContextInterface&FileContext $context
     * @return void
     */
    public function assemble(ContextInterface $context)
    {
        $context->getFileGenerator()->setDeclares([
            DeclareStatement::strictTypes(1)
        ]);
    }
}
