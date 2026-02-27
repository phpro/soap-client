<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type as MetadataType;

final readonly class TypeMap
{

    /**
     * @internal - Use TypeMap::fromMetadata instead
     *
     * @param array<array-key, Type> $types
     */
    public function __construct(
        private CodeGeneratorContext $codeGeneratorContext,
        private array $types
    ) {
    }

    public static function fromMetadata(
        CodeGeneratorContext $codeGeneratorContext,
        TypeCollection $types,
    ): self {
        return new self(
            $codeGeneratorContext,
            $types->map(function (MetadataType $type) use ($codeGeneratorContext) {
                return Type::fromMetadata($codeGeneratorContext, $type);
            })
        );
    }

    public function getCodeGeneratorContext(): CodeGeneratorContext
    {
        return $this->codeGeneratorContext;
    }

    /**
     * @return array<array-key, Type>
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
