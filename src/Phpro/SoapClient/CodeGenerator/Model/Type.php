<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
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
        private TypeNamespaceMap $namespaces,
        private string $xsdName,
        private string $name,
        private array $properties,
        private XsdType $xsdType
    ) {
        $this->meta = $xsdType->getMeta();
    }

    public static function fromMetadata(TypeNamespaceMap $namespaces, MetadataType $type): self
    {
        $xsdName = non_empty_string()->assert($type->getName());

        return new self(
            $namespaces,
            $xsdName,
            Normalizer::normalizeClassname($xsdName),
            array_map(
                function (MetadataProperty $property) use ($namespaces) {
                    return Property::fromMetaData(
                        $namespaces,
                        $property
                    );
                },
                iterator_to_array($type->getProperties())
            ),
            $type->getXsdType(),
        );
    }

    /**
     * @return non-empty-string
     */
    public function getNamespace(): string
    {
        return $this->namespaces->detectDestinationForType($this->xsdType)->namespace;
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
        $destination = $this->namespaces->detectDestinationForType($this->xsdType);
        $name = Normalizer::normalizeClassname($this->getName());
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
