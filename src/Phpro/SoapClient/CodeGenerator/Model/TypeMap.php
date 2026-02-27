<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
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
        private TypeNamespaceMap $namespaces,
        private array $types
    ) {
    }

    public static function fromMetadata(TypeNamespaceMap $namespaces, TypeCollection $types): self
    {
        return new self(
            $namespaces,
            $types->map(function (MetadataType $type) use ($namespaces) {
                return Type::fromMetadata($namespaces, $type);
            })
        );
    }

    public function getNamespaces(): TypeNamespaceMap
    {
        return $this->namespaces;
    }

    /**
     * @return array<array-key, Type>
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
