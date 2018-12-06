<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type as MetadataType;

/**
 * Class TypeMap
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class TypeMap
{

    /**
     * @var array|Type[]
     */
    private $types;

    /**
     * @var string
     */
    private $namespace;

    /**
     * TypeMap constructor.
     *
     * @param string $namespace
     * @param array|Type[] $types
     */
    public function __construct(string $namespace, array $types)
    {
        $this->namespace = Normalizer::normalizeNamespace($namespace);
        $this->types = $types;
    }

    public static function fromMetadata(string $namespace, TypeCollection $types): self
    {
        return new self(
            $namespace,
            $types->map(function (MetadataType $type) use ($namespace) {
                return Type::fromMetadata($namespace, $type);
            })
        );
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return array|Type[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
