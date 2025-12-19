<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\TypeNameCalculator;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\MetaTypeEnhancer;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\TypeEnhancer;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\Property as MetadataProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use function Psl\Type\non_empty_string;

final class Property
{
    private TypeMeta $meta;

    private TypeEnhancer $typeEnhancer;

    /**
     * @internal
     *
     * @param non-empty-string $name
     * @param non-empty-string $type
     * @param non-empty-string $namespace
     */
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly TypeNamespaceMap $namespaces,
        private readonly string $namespace,
        private readonly XsdType $xsdType
    ) {
        $this->meta = $xsdType->getMeta();
        $this->typeEnhancer = new MetaTypeEnhancer($this->meta);
    }

    public static function fromMetaData(TypeNamespaceMap $namespaces, MetadataProperty $property): self
    {
        $type = $property->getType();
        $typeName = $type->getName();
        $calculatedTypeName = Normalizer::normalizeDataType((new TypeNameCalculator())($type));
        $namespace = $namespaces->detectDestinationForType($type)->namespace;

        // This makes it possible to set FQCN as type names in the metadata through TypeReplacers.
        if (Normalizer::isConsideredExistingThirdPartyClass($typeName)) {
            $className = Normalizer::getClassNameFromFQN($typeName);
            $type = $type->copy($className)->withBaseType($className)->withMemberTypes([]);
            $namespace = Normalizer::getNamespaceFromFQN($typeName);
            $calculatedTypeName = $className;
        }

        return new self(
            Normalizer::normalizeProperty(non_empty_string()->assert($property->getName())),
            non_empty_string()->assert($calculatedTypeName),
            $namespaces,
            Normalizer::normalizeNamespace($namespace),
            $type
        );
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
    public function getType(): string
    {
        if (Normalizer::isKnownType($this->type)) {
            return $this->type;
        }

        if ($this->meta->isSimple()->unwrapOr(false)
                && !$this->meta->enums()->isSome()
                && Normalizer::isKnownType($this->xsdType->getBaseType())
        ) {
            return $this->xsdType->getBaseType();
        }

        return '\\'.$this->namespace.'\\'.Normalizer::normalizeClassname($this->type);
    }

    /**
     * @return non-empty-string
     */
    public function getPhpType(): string
    {
        return $this->typeEnhancer->asPhpType($this->getType());
    }

    /**
     * @return non-empty-string
     */
    public function getDocBlockType(): string
    {
        return $this->typeEnhancer->asDocBlockType($this->getType());
    }

    /**
     * @return non-empty-string
     */
    public function getterName(): string
    {
        return Normalizer::generatePropertyMethod('get', $this->getName());
    }

    /**
     * @return non-empty-string
     */
    public function setterName(): string
    {
        return Normalizer::generatePropertyMethod('set', $this->getName());
    }

    public function getXsdType(): XsdType
    {
        return $this->xsdType;
    }

    public function getMeta(): TypeMeta
    {
        return $this->meta;
    }

    public function getNamespaces(): TypeNamespaceMap
    {
        return $this->namespaces;
    }

    /**
     * @param callable(TypeMeta): TypeMeta $metaProvider
     */
    public function withMeta(callable $metaProvider): self
    {
        $meta = $metaProvider($this->meta);

        $new = clone $this;
        $new->meta = $meta;
        $new->typeEnhancer = new MetaTypeEnhancer($meta);

        return $new;
    }
}
