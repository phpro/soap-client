<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property as MetadataProperty;

/**
 * Class Property
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $namespace;

    /**
     * Property constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $namespace
     */
    public function __construct(string $name, string $type, string $namespace)
    {
        $this->name = Normalizer::normalizeProperty($name);
        $this->type = Normalizer::normalizeDataType($type);
        $this->namespace = Normalizer::normalizeNamespace($namespace);
    }

    public static function fromMetaData(string $namespace, MetadataProperty $property)
    {
        return new self(
            $property->getName(),
            $property->getType()->getBaseTypeOrFallbackToName(),
            $namespace
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
     * @return string
     */
    public function getType(): string
    {
        if (Normalizer::isKnownType($this->type)) {
            return $this->type;
        }

        return '\\'.$this->namespace.'\\'.Normalizer::normalizeClassname($this->type);
    }

    /**
     * @return string|null
     */
    public function getCodeReturnType(): ?string
    {
        $type = $this->getType();

        if ($type === 'mixed' && PHP_VERSION_ID < 80000) {
            return null;
        }

        return $type;
    }

    /**
     * @return string
     */
    public function getterName(): string
    {
        return Normalizer::generatePropertyMethod('get', $this->getName());
    }

    /**
     * @return string
     */
    public function setterName(): string
    {
        return Normalizer::generatePropertyMethod('set', $this->getName());
    }
}
