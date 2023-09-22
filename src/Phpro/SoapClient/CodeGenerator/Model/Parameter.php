<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\TypeEnhancer\Calculator\TypeNameCalculator;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\Parameter as MetadataParameter;
use Soap\Engine\Metadata\Model\TypeMeta;
use function Psl\Type\non_empty_string;

class Parameter
{
    /**
     * @var non-empty-string
     */
    private string $name;

    /**
     * @var non-empty-string
     */
    private string $type;

    /**
     * @var non-empty-string
     */
    private string $namespace;

    private TypeMeta $meta;

    /**
     * Parameter constructor.
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
    }

    /**
     * @param non-empty-string $parameterNamespace
     */
    public static function fromMetadata(string $parameterNamespace, MetadataParameter $parameter): Parameter
    {
        $type = $parameter->getType();
        $meta = $type->getMeta();
        $typeName = (new TypeNameCalculator())($type);

        return new self(
            non_empty_string()->assert($parameter->getName()),
            non_empty_string()->assert($typeName),
            $parameterNamespace,
            $meta
        );
    }

    /**
     * @return string
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
     * Get an array representation for creating a Generator
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->getType(),
        ];
    }

    public function getMeta(): TypeMeta
    {
        return $this->meta;
    }
}
