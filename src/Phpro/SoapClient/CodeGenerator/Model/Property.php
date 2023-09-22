<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\TypeNameCalculator;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\MetaTypeEnhancer;
use Phpro\SoapClient\CodeGenerator\TypeEnhancer\TypeEnhancer;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Metadata;
use Soap\Engine\Metadata\Model\Property as MetadataProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use function Psl\Type\non_empty_string;

/**
 * Class Property
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class Property
{
    /**
     * @var non-empty-string
     */
    private $name;

    /**
     * @var non-empty-string
     */
    private $type;

    /**
     * @var non-empty-string
     */
    private $namespace;

    private TypeMeta $meta;

    private TypeEnhancer $typeEnhancer;

    /**
     * Property constructor.
     *
     * @param non-empty-string $name
     * @param non-empty-string $type
     * @param non-empty-string $namespace
     */
    public function __construct(string $name, string $type, string $namespace, TypeMeta $meta)
    {
        $this->name = Normalizer::normalizeProperty($name);
        $this->type = Normalizer::normalizeDataType($type);
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        $this->meta = $meta;
        $this->typeEnhancer = new MetaTypeEnhancer($this->meta);
    }

    /**
     * @param non-empty-string $namespace
     */
    public static function fromMetaData(string $namespace, MetadataProperty $property)
    {
        $type = $property->getType();
        $meta = $type->getMeta();
        $typeName = (new TypeNameCalculator())($type);

        return new self(
            non_empty_string()->assert($property->getName()),
            non_empty_string()->assert($typeName),
            $namespace,
            $meta
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

    public function getMeta(): TypeMeta
    {
        return $this->meta;
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
