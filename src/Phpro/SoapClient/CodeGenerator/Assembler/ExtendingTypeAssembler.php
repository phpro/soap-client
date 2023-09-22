<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\AssemblerException;

/**
 * Class ExtendingTypeAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class ExtendingTypeAssembler implements AssemblerInterface
{
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
        $type = $context->getType();
        $meta = $type->getMeta();
        $extending = $meta->extends()->unwrapOr(null);

        if (!$extending || ($extending['isSimple'] ?? false)) {
            return;
        }

        $namespace = $type->getNamespace();
        $typeName = Normalizer::normalizeClassname($extending['type']);
        $extendedClassName = sprintf('\\%s\\%s', $namespace, $typeName);

        try {
            $extendAssembler = new ExtendAssembler($extendedClassName);
            if ($extendAssembler->canAssemble($context)) {
                $extendAssembler->assemble($context);
            }
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
