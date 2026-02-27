<?php

namespace Phpro\SoapClient\CodeGenerator;

use Laminas\Code\Generator\DocBlockGenerator;
use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Laminas\Code\Generator\EnumGenerator\EnumGenerator;
use Laminas\Code\Generator\FileGenerator;
use Soap\Engine\Metadata\Model\XsdType;
use function Psl\Dict\pull;
use function Psl\Type\int;

/**
 * @template-implements GeneratorInterface<Type>
 */
class EnumerationGenerator implements GeneratorInterface
{
    /**
     * @param FileGenerator $file
     * @param Type $context
     * @return string
     */
    public function generate(FileGenerator $file, $context): string
    {
        $file->setNamespace($context->getNamespace());
        $file->setBody($this->generateBody($context));

        return $file->generate();
    }

    private function generateBody(Type $type): string
    {
        $xsdType = $type->getXsdType();
        $xsdMeta = $xsdType->getMeta();
        $codingStandards = $type->getCodeGeneratorContext()->codingStandards;
        $enumType = match ($xsdType->getBaseType()) {
            'int', 'integer' => 'int',
            default => 'string',
        };

        $body = EnumGenerator::withConfig([
            'name' => $type->getName(),
            'backedCases' => [
                'type' => $enumType,
                'cases' => $this->buildCases($xsdType, $enumType, $codingStandards),
            ]
        ])->generate();

        if ($docs = $xsdMeta->docs()->unwrapOr('')) {
            $docblock = (new DocBlockGenerator())
                ->setWordWrap(false)
                ->setLongDescription($docs)
                ->generate();
            $body = $docblock . $body;
        }

        return $body;
    }

    /**
     * @param 'string'|'int' $enumType
     * @return array<string, int|string>
     */
    private function buildCases(
        XsdType $xsdType,
        string $enumType,
        CodingStandardsStrategyInterface $codingStandards
    ): array {
        $enums = $xsdType->getMeta()->enums()->unwrapOr([]);

        return pull(
            $enums,
            static fn(string $value): int|string => match ($enumType) {
                'int' => int()->coerce($value),
                'string' => $value,
            },
            static fn(string $value): string => $codingStandards->normalizeEnumCaseName($value)
        );
    }
}
