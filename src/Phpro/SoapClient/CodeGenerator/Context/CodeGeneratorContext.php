<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;

final readonly class CodeGeneratorContext
{
    public function __construct(
        public TypeNamespaceMap $typeNamespaceMap,
        public CodingStandardsStrategyInterface $codingStandards,
    ) {
    }
}
