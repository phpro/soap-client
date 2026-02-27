<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\Property as MetadataProperty;
use Soap\Engine\Metadata\Model\Type as MetadataType;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use SplFileInfo;
use function Psl\Type\non_empty_string;

final readonly class Type
{
    private TypeMeta $meta;

    /**
     * @internal - Use Type::fromMetadata instead
     *
     * @param non-empty-string $xsdName
     * @param non-empty-string $name
     * @param array<array-key, Property> $properties
     */
    public function __construct(
        private CodeGeneratorContext $codeGeneratorContext,
        private string $xsdName,
        private string $name,
        private array $properties,
        private XsdType $xsdType,
    ) {
        $this->meta = $xsdType->getMeta();
    }

    public static function fromMetadata(
        CodeGeneratorContext $codeGeneratorContext,
        MetadataType $type,
    ): self {
        $xsdName = non_empty_string()->assert($type->getName());

        return new self(
            $codeGeneratorContext,
            $xsdName,
            $codeGeneratorContext->codingStandards->normalizeTypeName($xsdName),
            array_map(
                function (MetadataProperty $property) use ($codeGeneratorContext) {
                    return Property::fromMetaData(
                        $codeGeneratorContext,
                        $property,
                    );
                },
                iterator_to_array($type->getProperties())
            ),
            $type->getXsdType(),
        );
    }

    public function getCodeGeneratorContext(): CodeGeneratorContext
    {
        return $this->codeGeneratorContext;
    }

    /**
     * @return non-empty-string
     */
    public function getNamespace(): string
    {
        return $this->codeGeneratorContext->typeNamespaceMap->detectDestinationForType($this->xsdType)->namespace;
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function getXsdName(): string
    {
        return $this->xsdName;
    }

    public function getFileInfo(): SplFileInfo
    {
        $destination = $this->codeGeneratorContext->typeNamespaceMap->detectDestinationForType($this->xsdType);
        $name = $this->getName();
        $path = rtrim($destination->path, '/\\').'/'.$name.'.php';

        return new SplFileInfo($path);
    }

    /**
     * @return non-empty-string
     */
    public function getFullName(): string
    {
        $fqnName = sprintf('%s\\%s', $this->getNamespace(), $this->getName());

        return Normalizer::normalizeNamespace($fqnName);
    }

    /**
     * @return array<array-key, Property>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getXsdType(): XsdType
    {
        return $this->xsdType;
    }

    public function getMeta(): TypeMeta
    {
        return $this->meta;
    }
}
