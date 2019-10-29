<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\FileContext;
use Phpro\SoapClient\Exception\AssemblerException;
use Zend\Code\DeclareStatement;

/**
 * Class FileAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class FileAssembler implements AssemblerInterface
{
    /**
     * @var FileAssemblerOptions
     */
    private $options;

    /**
     * @param FileAssemblerOptions|null $options
     */
    public function __construct(FileAssemblerOptions $options = null)
    {
        $this->options = $options ?? new FileAssemblerOptions();
    }

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
        if ($this->options->useStrictTypes()) {
            $context->getFileGenerator()->setDeclares([
                DeclareStatement::strictTypes(1)
            ]);
        }
    }
}
